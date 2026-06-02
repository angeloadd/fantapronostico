<x-prediction::shared.card-header
    :$homeTeamName
    :$awayTeamName
    :$homeTeamCode
    :$awayTeamCode
    :$startedAt
    :$isGameInTheFuture
/>
<form
    class="w-full flex flex-col justify-around items-center text-lg space-y-2 sm:space-y-4"
    action="{{$action}}"
    method="POST"
>
    @method($method)
    @csrf
    <x-prediction::shared.score
        :$homeTeamName
        :$awayTeamName
        :$prediction
    />

    <x-prediction::shared.sign
        :$homeTeamName
        :$awayTeamName
        :$prediction
    />

    <x-prediction::shared.scorers
        :$homeTeamName
        :$awayTeamName
        :$homeTeamPlayers
        :$awayTeamPlayers
        :$isGroupStage
        :$prediction
    />
    <button type="submit" class="btn {{$btnBg}} w-full fp2024-title">{{$btnText}}</button>
</form>
