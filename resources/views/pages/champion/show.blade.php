<x-champion::shared.layout>
    <x-champion::shared.card status="edit">
        <x-champion::shared.card-header :$tournamentLogo :$tournamentName :$firstMatchDate text="Modifica il pronostico entro la data di inizio dell'Europeo"/>
        <x-champion::shared.table :champions="collect([$champion])"/>
        <a href="{{route('champion.edit', compact('champion'))}}" class="btn btn-primary w-full text-base-100">
            Modifica Pronostico
        </a>
    </x-champion::shared.card>
</x-champion::shared.layout>
