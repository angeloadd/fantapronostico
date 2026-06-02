<div class="flex w-full justify-center items-center">
    <x-prediction::shared.score-input label="home_score" :teamName="$homeTeamName" :$prediction/>
    <div class="px-2">:</div>
    <x-prediction::shared.score-input label="away_score" :teamName="$awayTeamName" :$prediction/>
</div>
