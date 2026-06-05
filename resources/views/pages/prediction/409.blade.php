<x-prediction::shared.layout>
    <x-prediction::shared.game-bar :games="$games" :game="$game"/>
    <div class="w-full flex justify-center items-center text-base-content/80 text-center">
        <x-prediction::shared.card status="disabled">
            <x-prediction::shared.card-header
                    :homeTeamName="$game->home_team->name"
                    :awayTeamName="$game->away_team->name"
                    :homeTeamCode="$game->home_team->code"
                    :awayTeamCode="$game->away_team->code"
                    :startedAt="$game->started_at"
                    :isGameInTheFuture="$game->started_at->isFuture()"
                    :countdownDeadline="$game->predictable_from"
                    :isCountdownOpen="false"
            />
            <p class="mt-2">
                {{ __('messages.prediction.available_from') }} <strong>
                    {{str($game->predictable_from->avoidMutation()->timezone('Europe/Rome')->isoFormat('D MMMM YYYY \a\l\l\e HH:mm'))->title()->replace('Alle','alle')}}
                </strong>
            </p>
        </x-prediction::shared.card>
    </div>
</x-prediction::shared.layout>
