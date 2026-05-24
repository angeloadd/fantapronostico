<li class="w-full">
    <a href="{{route($routeName)}}"
        @class([
             'text-secondary-content',
             'bg-accent hover:bg-accent' => str_contains(Route::currentRouteName(), $active ?? $routeName),
             'hover:bg-accent/50' => !str_contains(Route::currentRouteName(), $active ?? $routeName)
        ])
    >
        <img class="me-2"
             width="20px"
             src="{{Vite::asset('resources/assets/images/'.$svg.'.svg')}}"
             alt="dashboard"/>
        {{$text}}
    </a>
</li>
