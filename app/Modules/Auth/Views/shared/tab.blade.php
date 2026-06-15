<a
    href="{{route($name)}}"
    role="tab"
    @class([
        'flex-1 text-center py-3 text-base-content transition-colors text-title tracking-wide text-lg',
        'border-b-2 border-primary text-base-content -mb-px' => Route::currentRouteName() === $name,
        'text-base-content/50 hover:text-base-content/80' => Route::currentRouteName() !== $name,
    ])
>
    {{$text}}
</a>
