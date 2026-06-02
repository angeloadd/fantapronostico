<x-champion::shared.layout>
    <x-champion::shared.card>
        <x-champion::shared.card-header
            :$tournamentLogo
            :$tournamentName
            :$firstMatchDate
            text="Inserici il pronostico entro la data di inizio dell'Europeo"
        />
        <x-champion::shared.form
            btnText="Pronostica"
            btnBg="bg-accent"
            method="POST"
            action="{{route('champion.store')}}"
            :$teams
            :$players
        />
    </x-champion::shared.card>

</x-champion::shared.layout>
