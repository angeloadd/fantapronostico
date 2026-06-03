<x-champion::shared.layout>
    <x-champion::shared.card status="edit">
        <x-champion::shared.card-header :$tournamentLogo :$tournamentName :$firstMatchDate text="{{ __('messages.common.edit') }}"/>
        <x-champion::shared.form
            btnText="{{ __('messages.prediction.edit_title') }}"
            btnBg="bg-primary"
            method="POST"
            action="{{route('champion.update', compact('champion'))}}"
            :$teams
            :$players
            :prediction="$champion"
        />
    </x-champion::shared.card>

</x-champion::shared.layout>
