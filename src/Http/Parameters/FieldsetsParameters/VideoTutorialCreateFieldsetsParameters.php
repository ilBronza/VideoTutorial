<?php

namespace IlBronza\VideoTutorial\Http\Parameters\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
use Illuminate\Validation\Rule;

class VideoTutorialCreateFieldsetsParameters extends FieldsetParametersFile
{
    public function _getFieldsetsParameters() : array
    {
        $model = app(config('videotutorial.models.videotutorial.class'));

        $parameters = [
            'base' => [
                'translationPrefix' => 'videotutorial::fields',
                'fields' => [
                    'parent' => [
                        'type' => 'select',
                        'multiple' => false,
                        'rules' => [
                            'nullable',
                            'uuid',
                            Rule::exists($model::class, $model->getKeyName())->withoutTrashed($model->getDeletedAtColumn()),
                        ],
                        'relation' => 'parent'
                    ],

                    'name' => ['text' => 'string|required|max:255'],
                    'title' => ['text' => 'string|required|max:255'],

                    'short_description' => ['texteditor' => 'string|required|max:1024'],
                    'description' => ['texteditor' => 'string|required|max:102400'],

                    'sorting_index' => [
                        'number' => 'integer|nullable|min:0',
                    ],
                    'link' => ['text' => 'string|nullable|max:255'],
                    'embed' => ['textarea' => 'string|nullable|max:102400'],

                    'active' => ['boolean' => 'boolean|required'],
                    'show' => ['boolean' => 'boolean|nullable'],

                    'file' => [
                        'type' => 'file',
                        'multiple' => false,
                        'rules' =>'file|nullable|max:' . config('media-library.max_file_size')
                    ],
                ],
                'width' => ['1-2@m']
            ]
        ];

        if ($model->hasRouteNamesColumn()) {
            $routeNames = collect(Router::getRoutes()->getRoutes())
                ->filter(static fn (Route $route) => in_array('GET', $route->methods(), true))
                ->map(static fn (Route $route) => $route->getName())
                ->filter(static fn (?string $name) => $name !== null && $name !== '')
                ->unique()
                ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                ->values()
                ->all();

            $parameters['base']['fields']['route_names'] = [
                'type' => 'select',
                'multiple' => true,
                'list' => array_combine($routeNames, $routeNames),
                'rules' => [
                    'nullable',
                    'array',
                    'list',
                    Rule::in($routeNames),
                ],
            ];
        }

        return $parameters;
    }
}

