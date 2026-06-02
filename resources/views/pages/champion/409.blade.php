<x-champion::shared.layout>
    <div class="p-2 sm:p-8 pt-8 flex justify-center items-center">
        <div role="alert" class="alert alert-warning flex flex-col justify-center items-center shadow-lg">
            <x-champion::shared.card-header
                text="<span class='font-normal text-neutral'>Il pronostico Vincente e Capocannoniere sarà aperto dal</span>
             <span class='text-neutral'>{{str($championSettableFrom->timezone('Europe/Berlin')->isoFormat('D MMMM YYYY \a\l\l\e H:mm'))->replaceMatches('/(?<=\s)[a-z]/', static fn($match) => ucfirst($match[0]), 1)}}</span>"
                :tournamentLogo="\App\Models\Tournament::first()?->logo"
                :tournamentName="\App\Models\Tournament::first()?->name"
                :firstMatchDate="\App\Models\Tournament::first()?->started_at"
                countdownBg="bg-yellow-500"
            />
        </div>
    </div>
</x-champion::shared.layout>
