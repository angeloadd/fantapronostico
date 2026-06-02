<div>
    <h3 class="font-bold text-center text-sm sm:text-lg pb-3">{!! $text !!}</h3>
    <div class="flex items-center justify-center">
        <div class="w-12 h-12 sm:h-36 sm:w-36 flex justify-center items-center">
            <img
                src="{{$tournamentLogo}}"
                class="object-cover object-center overflow-hidden"
                alt="{{$tournamentName}} Logo"
            >
        </div>
        <x-partials.countdown.main bgColor="{{$countdownBg ?? 'bg-primary/80'}}" :date="$firstMatchDate"/>
    </div>
</div>
