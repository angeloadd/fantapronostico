@php
    $isCurrentRoute = str_contains(Route::currentRouteName(), $routeName)
@endphp

<li>
    <a href="{{route($routeName)}}"
            @class([
                 'rounded-2xl',
                 'bg-[#2b3b5a] text-accent border-l-accent border-l-2 hover:text-primary-content/85' => $isCurrentRoute,
                 'text-primary-content/85 hover:bg-[#2b3b5a]' => !$isCurrentRoute,
            ])
    >
        <x-dynamic-component :component="'partials.svgs.'.$svg"/>
        <span class="pl-1">{{$text}}</span>
    </a>
</li>
