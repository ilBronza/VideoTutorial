<?php

namespace IlBronza\VideoTutorial\Models;

use IlBronza\CRUD\Models\BaseModel;
use IlBronza\CRUD\Traits\Media\InteractsWithMedia;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\VideoTutorial\Facades\VideoTutorial as FacadeVideoTutorial;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;

class Videotutorial extends BaseModel implements HasMedia
{
	use CRUDUseUuidTrait;
    use InteractsWithMedia;

	use PackagedModelsTrait;
	use CRUDParentingTrait;

    public ? string $translationFolderPrefix = 'videotutorial';
	static $packageConfigPrefix = 'videotutorial';
	static $modelConfigPrefix = 'videotutorial';
	static $parentKeyName = 'parent_id';

	static $deletingRelationships = ['media'];

    protected $keyType = 'string';

    protected $casts = [
        'deleted_at' => 'datetime',
        'route_names' => 'array',
        'active' => 'boolean',
        'show' => 'boolean',
    ];

    public function scopeVisibleInCatalog(Builder $query): Builder
    {
        return $query->where('active', true)->where('show', true);
    }

    public function isVisibleInCatalog(): bool
    {
        return $this->active && $this->show;
    }

    public function getFrontendShowUrl(): string
    {
        return FacadeVideoTutorial::route('frontend.videotutorials.show', [
            $this,
        ]);
    }

    public function getEditParametersFile(): string
    {
        return $this->getConfigByKey('parametersFiles.edit')
            ?? $this->getCompulsoryConfigByKey('parametersFiles.create');
    }

    protected function plainShortDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => html_entity_decode(strip_tags((string) $this->short_description), ENT_QUOTES | ENT_HTML5),
            set: fn (?string $value) => [
                'short_description' => $value === null ? null : e($value),
            ],
        );
    }

    protected function plainDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => html_entity_decode(strip_tags((string) $this->description), ENT_QUOTES | ENT_HTML5),
            set: fn (?string $value) => [
                'description' => $value === null ? null : e($value),
            ],
        );
    }

    public function getParentPossibleValuesArray(): array
    {
        $parentKey = $this->getParentKeyName();
        $nameField = $this->getNameFieldName();
        $tutorials = $this->newQuery()->get([
            $this->getKeyName(),
            $parentKey,
            $nameField,
        ]);

        if ($this->exists) {
            $children = $tutorials->groupBy($parentKey);
            $pending = [
                $this->getKey(),
            ];
            $excluded = [];

            while ($pending !== []) {
                $key = array_pop($pending);

                if (isset($excluded[$key])) {
                    continue;
                }

                $excluded[$key] = true;

                foreach ($children->get($key, []) as $child) {
                    $pending[] = $child->getKey();
                }
            }

            $tutorials = $tutorials->reject(fn (self $tutorial) => isset($excluded[$tutorial->getKey()]));
        }

        return $tutorials->pluck($nameField, $this->getKeyName())->all();
    }

    public function save(array $options = [])
    {
        $parentKey = $this->getParentKeyName();

        if (!$this->isDirty($parentKey) || $this->{$parentKey} === null) {
            return parent::save($options);
        }

        return $this->getConnection()->transaction(function () use ($options, $parentKey) {
            $ancestorId = $this->{$parentKey};
            $visited = [];

            if ($this->exists) {
                $visited[$this->getKey()] = true;
            }

            // Hold ancestor locks until the parent change has been written.
            while ($ancestorId !== null) {
                $ancestor = $this->newQuery()->lockForUpdate()->find($ancestorId);

                if (!$ancestor || isset($visited[$ancestor->getKey()])) {
                    throw ValidationException::withMessages([
                        'parent' => __('videotutorial::videotutorial.invalidParent'),
                    ]);
                }

                $visited[$ancestor->getKey()] = true;
                $ancestorId = $ancestor->{$parentKey};
            }

            return parent::save($options);
        });
    }

    public function delete()
    {
        if (!$this->exists) {
            return parent::delete();
        }

        return $this->getConnection()->transaction(function () {
            $this->newQuery()->withTrashed()->lockForUpdate()->find($this->getKey());

            return parent::delete();
        });
    }

    public function hasRouteNamesColumn(): bool
    {
        return $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'route_names');
    }

	public static function boot() {

		parent::boot();

	    static::deleting(function($model)
	    {
	        foreach($model->children()->get() as $child)
	        {
	            $child->parent_id = null;
	            $child->saveQuietly();
	        }
	    });
	}

	public function getIndexUrl(array $data = [])
	{
		return FacadeVideoTutorial::route('frontend.videotutorials.index');
	}

}
