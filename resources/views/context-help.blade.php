<div id="{{ $modalId }}" aria-labelledby="{{ $modalId }}-title" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close
                aria-label="{{ __('videotutorial::videotutorial.closeHelp') }}"></button>
        <h2 id="{{ $modalId }}-title" class="uk-modal-title">{{ __('videotutorial::videotutorial.contextHelp') }}</h2>

        @foreach ($videos as $video)
            <article class="uk-margin">
                <h3 class="uk-h4 uk-margin-small-bottom">{{ $video['title'] }}</h3>
                @if ($video['description'])
                    <p class="uk-margin-small">{{ $video['description'] }}</p>
                @endif

                @if ($video['type'] === 'file')
                    <video src="{{ $video['url'] }}" controls preload="none" playsinline uk-video="autoplay: false"
                           aria-label="{{ $video['title'] }}" class="uk-width-1-1"></video>
                @elseif ($video['type'] === 'embed')
                    <iframe src="{{ $video['url'] }}" title="{{ $video['title'] }}" loading="lazy" uk-video="autoplay: false"
                            sandbox="allow-scripts allow-presentation" allowfullscreen
                            style="width: 100%; aspect-ratio: 16 / 9; border: 0"></iframe>
                @else
                    <a href="{{ $video['url'] }}" target="_blank" rel="noopener noreferrer" class="uk-button uk-button-default">
                        {{ __('videotutorial::videotutorial.openVideo') }}
                    </a>
                @endif
            </article>
        @endforeach
    </div>
</div>
