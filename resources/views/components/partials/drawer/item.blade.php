@php
    $isCurrentRoute = str_contains(Route::currentRouteName(), $active ?? $routeName)
@endphp

1<li class="p-1">
    <a href="{{route($routeName)}}"
        @class([
             'bg-[#2b3b5a] text-accent border-l-accent border-l-2 rounded-2xl hover:text-base-100' => str_contains(Route::currentRouteName(), $active ?? $routeName),
             'text-primary-content/85 hover:bg-[#2b3b5a]0' => !str_contains(Route::currentRouteName(), $active ?? $routeName)
        ])
    >
        <x-dynamic-component :component="'partials.svgs.'.$svg"/>
        <span class="pl-1">{{$text}}</span>
    </a>
</li>
