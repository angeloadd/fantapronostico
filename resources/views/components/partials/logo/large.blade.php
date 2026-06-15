<a class="text-title text-2xl tracking-wide -space-x-0.5 {{$textColor ?? 'text-primary-content'}}" href="{{route('home')}}">
    <span>FANTA</span>
    <x-partials.logo.svg :primary="$primary ?? null" :secondary="$secondary ?? null" width="{{$width ?? 'w-full'}}"/>
    <span>PRONOSTICO</span>
</a>
