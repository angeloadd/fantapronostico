<x-champion::shared.layout>
    <x-champion::shared.card :isForm="true">
        <x-champion::shared.card-header
            :$tournamentLogo
            :$tournamentName
            :$firstMatchDate
            text="{{ __('messages.champion.deadline_hint') }}"
        />
        <x-champion::shared.form
            btnText="{{ __('messages.common.predict') }}"
            btnTheme="accent"
            method="POST"
            action="{{route('champion.store')}}"
            :$teams
            :$players
            :prediction="null"
        />
    </x-champion::shared.card>

</x-champion::shared.layout>
