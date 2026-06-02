<x-prediction::shared.layout>
    <x-prediction::shared.game-bar :games="$games" :game="$game"/>
    <div>
        <x-prediction::shared.card>
            <x-prediction::shared.form
                method="POST"
                action="{{route('prediction.store', ['game' => $game])}}"
                :homeTeamName="__($game->home_team->name)"
                :awayTeamName="__($game->away_team->name)"
                :homeTeamLogo="$game->home_team->logo"
                :awayTeamLogo="$game->away_team->logo"
                :$homeTeamPlayers
                :$awayTeamPlayers
                :startedAt="$game->started_at"
                :isGameInTheFuture="$game->started_at->isFuture()"
                :isGroupStage="$game->isGroupStage()"
                :btnText="__('Pronostica')"
                btnBg="bg-accent"
                :prediction="null"
            />
        </x-prediction::shared.card>
    </div>

</x-prediction::shared.layout>
