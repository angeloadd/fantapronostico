<div class="w-full flex flex-col items-center justify-center">
    <div class="flex w-full justify-end">
        <x-partials.countdown.main :date="$championSettableFrom ?? $firstMatchDate" :isOpen="($championSettableFrom ?? null) === null" :isExpired="($championSettableFrom ?? $firstMatchDate)?->isPast() ?? false"/>
    </div>
        <img
                src="{{Vite::asset('resources/assets/images/coppaWorldCup.png')}}"
                class="w-1/6"
                alt="{{$tournamentName}} Logo"
        >
    <p class="font-bold text-center text-sm sm:text-md p-4 text-base-content/80">{!! $text !!}</p>
</div>
