<div class="w-full">
    <div class="flex w-full justify-end">
        <x-partials.countdown.main :date="$championSettableFrom ?? $firstMatchDate" :isOpen="($championSettableFrom ?? null) === null" :isExpired="($championSettableFrom ?? $firstMatchDate)?->isPast() ?? false"/>
    </div>
    <div class="flex items-center justify-center">
        <div class="w-12 h-12 sm:h-36 sm:w-36 flex justify-center items-center">
            <img
                src="{{$tournamentLogo}}"
                class="object-cover object-center overflow-hidden"
                alt="{{$tournamentName}} Logo"
            >
        </div>
    </div>
    <p class="font-bold text-center text-sm sm:text-md p-4 text-base-content/80">{!! $text !!}</p>
</div>
