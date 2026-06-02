<div class="w-full flex flex-col justify-center items-center">
    @if($isGameInTheFuture)
        <div class="ml-auto pb-2 sm:pb-3">
            <x-partials.countdown.main :date="$countdownDeadline ?? $startedAt" :isOpen="$isCountdownOpen ?? true" :isCountdownExpired="$isExpired ?? false"/>
        </div>
    @endif
    <div class="w-full flex items-center">
        <x-home::shared.team-display :teamCode="$homeTeamCode" :teamName="$homeTeamName"/>
        <x-home::shared.game-date :date="$startedAt"/>
        <x-home::shared.team-display :teamCode="$awayTeamCode" :teamName="$awayTeamName"/>
    </div>
</div>
