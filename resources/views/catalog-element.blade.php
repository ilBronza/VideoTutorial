<li class="dd-item" data-key="{{ $element->getKey() }}" data-id="{{ $element->getNestableKey() }}" id="{{ $element->getNestableKey() }}">
    <div class="dd-content">
        <a href="{{ $element->getFrontendShowUrl() }}">{{ $element->getNestableName() }}</a>
    </div>

    @if (isset($element->childs) && $element->childs->isNotEmpty())
        <ol class="dd-list">
            @foreach ($element->childs as $child)
                @include($nestableElementViewName, ['element' => $child])
            @endforeach
        </ol>
    @endif
</li>
