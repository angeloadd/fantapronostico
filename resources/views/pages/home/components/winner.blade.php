<x-home::shared.card title="Vincitore">
    <div class="w-full h-full flex justify-center items-center">
        <img class="w-1/3 h-32" src="{{Vite::asset('resources/assets/images/award.svg')}}" alt="awards ceremony illustration">
        <div class="text-center">
            <p class="font-normal">Il vincitore della lega {{ $leagueName }} è</p>
            <h2 class="text-6xl fp2024-title font-bold mb-0">{{ $winnerName }}</h2>
        </div>
    </div>
</x-home::shared.card>
