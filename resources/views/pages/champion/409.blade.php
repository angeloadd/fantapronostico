<x-champion::shared.layout>
    <x-champion::shared.card status="disabled">
        <x-champion::shared.card-header
                :$tournamentLogo
                :$tournamentName
                :$firstMatchDate
                text="{{ __('messages.champion.not_open') }}"
                :$championSettableFrom
        />
    </x-champion::shared.card>
</x-champion::shared.layout>
