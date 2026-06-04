<a
    href="{{route($name)}}"
    role="tab"
    @class([
        'flex-1 text-center py-3 text-base transition-colors text-title',
        'border-b-2 border-primary text-primary font-semibold -mb-px' => Route::currentRouteName() === $name,
        'text-base-content/50 hover:text-base-content/80' => Route::currentRouteName() !== $name,
    ])
>
    {{$text}}
</a>
