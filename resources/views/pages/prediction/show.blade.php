<x-prediction::shared.layout>
    <x-prediction::shared.game-bar :games="$games" :game="$game"/>
    <x-prediction::shared.card :large="true">
        <x-prediction::shared.deadline :startedAt="$game->started_at"/>
        <x-prediction::shared.table
            :$game
            :isGroupStage="$game->isGroupStage()"
        >
            <x-prediction::shared.table-row
                :prediction="$prediction"
                :lastUpdate="$prediction->updated_at->format('d/m/Y \o\r\e H:i:s \e u \m\s')"
                key=""
            />
        </x-prediction::shared.table>
        <a href="{{route('prediction.edit', ['prediction'=> $prediction])}}" class="btn btn-primary text-base-100 w-full mt-2 sm:mt-8">
            Modifica Pronostico
        </a>
    </x-prediction::shared.card>
</x-prediction::shared.layout>
