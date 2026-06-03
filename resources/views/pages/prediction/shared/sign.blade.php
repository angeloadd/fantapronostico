<div class="flex items-center justify-between w-full">
    <x-prediction::shared.sign-radio
        :TeamName="$homeTeamName"
        value="1"
        label="home_victory"
        :$prediction
    />
    <x-prediction::shared.sign-radio
        teamName="{{ __('messages.common.draw') }}"
        value="x"
        label="draw"
        :$prediction
    />
    <x-prediction::shared.sign-radio
        :teamName="$awayTeamName"
        value="2"
        label="away_victory"
        :$prediction
    />
</div>
