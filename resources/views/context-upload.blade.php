<div id="{{ $modalId }}" aria-labelledby="{{ $modalId }}-title" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close
                aria-label="{{ __('videotutorial::videotutorial.closeHelp') }}"></button>
        <h2 id="{{ $modalId }}-title" class="uk-modal-title">{{ __('videotutorial::videotutorial.uploadForPage') }}</h2>

        @if ($uploadUrl)
            <form method="POST" action="{{ $uploadUrl }}" enctype="multipart/form-data" class="uk-form-stacked">
                @csrf
                <div class="uk-margin">
                    <label class="uk-form-label" for="{{ $modalId }}-file">{{ __('videotutorial::videotutorial.videoFile') }}</label>
                    <input class="uk-input" id="{{ $modalId }}-file" type="file" name="file" accept="video/*" required>
                    @error('file', 'contextualVideoUpload')
                        <p class="uk-text-danger uk-margin-small-top" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <button class="uk-button uk-button-primary" type="submit">
                    {{ __('videotutorial::videotutorial.uploadVideo') }}
                </button>
            </form>
            @if ($errors->getBag('contextualVideoUpload')->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        UIkit.modal(document.getElementById(@json($modalId))).show();
                    });
                </script>
            @endif
        @else
            <p>{{ __('videotutorial::videotutorial.uploadUnavailable') }}</p>
        @endif
    </div>
</div>
