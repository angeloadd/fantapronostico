<li class="w-full px-5">
    <a href="{{route($routeName)}}"
        @class([
             'bg-accent hover:bg-accent' => str_contains(Route::currentRouteName(), $active ?? $routeName),
             'text-primary-content hover:bg-accent/70' => !str_contains(Route::currentRouteName(), $active ?? $routeName)
        ])
    >
        <img class="me-2"
             width="20px"
             src="{{Vite::asset('resources/assets/images/'.$svg.'.svg')}}"
             alt="{{$svg}}"/>
        {{$text}}
    </a>
</li>
