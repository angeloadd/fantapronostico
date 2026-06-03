<a
        @switch($svg)
            @case('previous') @keyup.left.window="window.location.assign('{{$link}}')" @break
            @case('next') @keyup.right.window="window.location.assign('{{$link}}')" @break
        @endswitch
        @class([
            'text-base-content/30 disabled pointer-events-none cursor-default text-decoration-none' => $disabled,
            'text-base-content' => !$disabled,
        ])
        href="{{$link}}"
>
    <x-dynamic-component :component="'partials.svgs.'.$svg"></x-dynamic-component>
</a>
