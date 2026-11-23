<?php

namespace IlBronza\VideoTutorial\View\Composers;

use IlBronza\VideoTutorial\VideoTutorial;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class ContextualVideoUpload
{
    private const MODAL_ID = 'videotutorial-context-upload';

    public function manageMenuButtons(): void
    {
        if (!VideoTutorial::canUploadForPage(auth()->user())) {
            return;
        }

        $account = app('menu')->getButtonByName('account');
        $account?->addChildFromArray([
            'name' => 'videotutorial.contextUpload',
            'text' => 'videotutorial::videotutorial.uploadForPage',
            'icon' => 'video',
            'href' => '#' . self::MODAL_ID,
            'toggle' => '#' . self::MODAL_ID,
        ]);
    }

    public function compose(View $view): void
    {
        $data = $view->getData();
        $iframed = $data['ib_IFRAMED'] ?? view()->shared('ib_IFRAMED', false);
        $user = auth()->user();

        if ($iframed || !app()->bound('menu') || !VideoTutorial::canUploadForPage($user)) {
            return;
        }

        $route = request()->route();
        $page = $route?->getName();
        $uploadUrl = $page && in_array('GET', $route->methods(), true)
            ? URL::signedRoute(config('videotutorial.routePrefix') . 'contextualUpload.store', [
                'page' => $page,
                'maker' => $user->getAuthIdentifier(),
            ])
            : null;

        app('menu')->setUsesCache(false);

        $extraViews = $data['extraViews'] ?? view()->shared('extraViews', []);
        $extraViews['bottom']['videotutorial::context-upload'] = [
            'modalId' => self::MODAL_ID,
            'uploadUrl' => $uploadUrl,
        ];
        $view->with('extraViews', $extraViews);
    }
}
