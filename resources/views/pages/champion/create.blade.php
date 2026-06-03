<x-champion::shared.layout>
    <x-champion::shared.card>
        <x-champion::shared.card-header
            :$tournamentLogo
            :$tournamentName
            :$firstMatchDate
            text="{{ __('messages.champion.deadline_hint') }}"
        />
        <x-champion::shared.form
            btnText="{{ __('messages.common.predict') }}"
            btnBg="bg-accent"
            method="POST"
            action="{{route('champion.store')}}"
            :$teams
            :$players
        />
    </x-champion::shared.card>

</x-champion::shared.layout>
