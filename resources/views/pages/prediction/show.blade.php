<x-prediction::shared.layout>
    <x-prediction::shared.game-bar :games="$games" :game="$game"/>
    <x-prediction::shared.card status="edit">
        <x-prediction::shared.card-header
                :homeTeamName="$game->home_team->name"
                :awayTeamName="$game->away_team->name"
                :homeTeamCode="$game->home_team->code"
                :awayTeamCode="$game->away_team->code"
                :startedAt="$game->started_at"
                :isGameInTheFuture="$game->started_at->isFuture()"
        />
        <div class="overflow-x-auto">
            <x-prediction::shared.table
                    :$game
                    :isGroupStage="$game->isGroupStage()"
            >
                <x-prediction::shared.table-row
                        :prediction="$prediction"
                        :lastUpdate="$prediction->updated_at->avoidMutation()->timezone('Europe/Rome')->format('d/m/Y \o\r\e H:i:s \e u \m\s')"
                        key=""
                />
            </x-prediction::shared.table>

        </div>
        <a href="{{route('prediction.edit', ['prediction'=> $prediction])}}" class="btn btn-primary text-primary-content w-full mt-2 sm:mt-8">
            Modifica Pronostico
        </a>
    </x-prediction::shared.card>
</x-prediction::shared.layout>
