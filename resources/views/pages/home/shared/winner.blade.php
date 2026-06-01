<div class="flex flex-col gap-2">
    <h2 class="text-xs font-medium text-base-content/50 uppercase tracking-wider">Vincitore</h2>
    <x-home::shared.card>
        <x-home::shared.illustration img="award.svg" alt="awards ceremony illustration">
            <p class="font-normal">Il vincitore della lega {{ $leagueName }} è</p>
            <h2 class="text-3xl fp2024-title font-bold">{{ $winnerName }}</h2>
        </x-home::shared.illustration>
    </x-home::shared.card>
</div>