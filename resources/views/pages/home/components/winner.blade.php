<x-home::shared.card title="{{ __('messages.home.winner_title') }}">
    <div class="w-full h-full flex justify-center items-center">
        <img class="w-1/3 h-32" src="{{Vite::asset('resources/assets/images/award.svg')}}" alt="{{ __('messages.home.winner_alt') }}">
        <div class="text-center">
            <p class="font-normal">{{ __('messages.home.winner_league_text') }} {{ $leagueName }} è</p>
            <h2 class="text-4xl lg:text-6xl fp2024-title font-bold mb-0">{{ $winnerName }}</h2>
        </div>
    </div>
</x-home::shared.card>
