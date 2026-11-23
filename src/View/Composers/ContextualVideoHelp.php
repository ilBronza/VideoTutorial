<?php

namespace IlBronza\VideoTutorial\View\Composers;

use DOMDocument;
use IlBronza\VideoTutorial\Models\Videotutorial;
use Illuminate\View\View;

class ContextualVideoHelp
{
    private const MODAL_ID = 'videotutorial-context-help';

    public function compose(View $view): void
    {
        $routeName = request()->route()?->getName();
        $data = $view->getData();
        $iframed = $data['ib_IFRAMED'] ?? view()->shared('ib_IFRAMED', false);

        if ($routeName === null || !auth()->check() || !app()->bound('menu') || $iframed) {
            return;
        }

        $model = app(config('videotutorial.models.videotutorial.class'));
        $schema = $model->getConnection()->getSchemaBuilder();

        if (!$schema->hasColumns($model->getTable(), [
            'route_names',
            'active',
            'show',
            'sorting_index',
            'name',
            'deleted_at',
        ])) {
            return;
        }

        $videos = $model->newQuery()
            ->whereJsonContains('route_names', $routeName)
            ->visibleInCatalog()
            ->with('media')
            ->orderBy('sorting_index')
            ->orderBy('name')
            ->get()
            ->map(fn (Videotutorial $tutorial) => $this->video($tutorial))
            ->filter();

        if ($videos->isEmpty()) {
            return;
        }

        $menu = app('menu');
        $navbar = $menu->provideMainRightBar();

        // The cached menu HTML cannot carry a button tied to this request's modal.
        $menu->setUsesCache(false);
        $menu->provideButton([
            'name' => 'videotutorial.contextHelp',
            'navbar' => $navbar->getName(),
            'icon' => 'circle-question',
            'href' => '#' . self::MODAL_ID,
            'toggle' => '#' . self::MODAL_ID,
            'translatedText' => '<span class="uk-hidden-visually">' . e(__('videotutorial::videotutorial.contextHelp')) . '</span>',
        ]);

        $extraViews = $data['extraViews'] ?? view()->shared('extraViews', []);
        $extraViews['bottom']['videotutorial::context-help'] = [
            'modalId' => self::MODAL_ID,
            'videos' => $videos,
        ];
        $view->with('extraViews', $extraViews);
    }

    private function video(Videotutorial $tutorial): ?array
    {
        $media = $tutorial->media->first(static fn ($media) => str_starts_with((string) $media->mime_type, 'video/'));

        if ($media) {
            $url = $media->getUrl();
            $type = 'file';
        } elseif ($url = $this->embedUrl($tutorial->embed)) {
            $type = 'embed';
        } else {
            $url = $this->webUrl($tutorial->link) ?? $this->webUrl($tutorial->file);
            $type = 'link';
        }

        if (!$url) {
            return null;
        }

        return [
            'title' => $tutorial->title ?: $tutorial->name,
            'description' => trim(html_entity_decode(strip_tags((string) $tutorial->short_description))),
            'url' => $url,
            'type' => $type,
        ];
    }

    private function embedUrl(?string $embed): ?string
    {
        if (!$embed || stripos($embed, '<iframe') === false) {
            return $this->webUrl($embed);
        }

        $document = new DOMDocument();
        $document->loadHTML($embed, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $iframe = $document->getElementsByTagName('iframe')->item(0);

        return $this->webUrl($iframe?->getAttribute('src'));
    }

    private function webUrl(?string $value): ?string
    {
        $url = trim((string) $value);

        return preg_match('~^https?://~i', $url) && filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }
}
