<x-prediction::shared.layout>
    <div class="py-8">
        <x-prediction::shared.card status="edit">
            <x-prediction::shared.form
                method="PUT"
                action="{{route('prediction.update', ['prediction' => $prediction])}}"
                :homeTeamName="__($game->home_team->name)"
                :awayTeamName="__($game->away_team->name)"
                :homeTeamCode="$game->home_team->code"
                :awayTeamCode="$game->away_team->code"
                :$homeTeamPlayers
                :$awayTeamPlayers
                :startedAt="$game->started_at"
                :isGameInTheFuture="$game->started_at->isFuture()"
                :isGroupStage="$game->isGroupStage()"
                :btnText="__('Modifica il Pronostico')"
                btnBg="bg-primary text-base-content-content"
                :prediction="$prediction ?? null"
            />
        </x-prediction::shared.card>
    </div>
</x-prediction::shared.layout>
