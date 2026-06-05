@php
    $currentRouteSub = explode('.', Route::currentRouteName())[0];
    $routeNameSub = explode('.',$routeName)[0];
    $isCurrentRoute = str_contains($currentRouteSub, $routeNameSub);
@endphp

<li>
    <a href="{{route($routeName)}}"
            @class([
                 'rounded-2xl',
                 'bg-[#2b3b5a] text-accent border-l-accent border-l-2 hover:text-primary-content/85 dark:bg-primary/50' => $isCurrentRoute,
                 'text-primary-content/85 hover:bg-[#2b3b5a] dark:hover:bg-primary/50' => !$isCurrentRoute,
            ])
    >
        <x-dynamic-component :component="'partials.svgs.'.$svg"/>
        <span class="pl-1">{{$text}}</span>
    </a>
</li>
