<form class="flex flex-col justify-center items-center gap-6" action="{{$action}}" method="POST">
    @csrf
    @method($method)
    <div class="w-full flex flex-col gap-2">
        <label for="winner" class="label">Vincente</label>
        <select name="winner"
                id="winner"
                class="select w-full bg-white dark:bg-neutral @error('winner') border-error @enderror"
        >
            <option
                    value=""
                    @selected(null === old('winner', $prediction?->team_id))
            >{{ __('messages.champion.select_winner') }}</option>
            @foreach($teams as $team)
                <option
                        value="{{$team->id}}"
                        @selected((int) old('winner', $prediction?->team_id) === $team->id)>{{__($team->name)}}
                </option>
            @endforeach
        </select>
        @error('winner')
        <span class="text-error text-xs">{{ __('messages.common.required') }}</span>
        @enderror
    </div>
    <div class="w-full flex flex-col gap-2">
        <label for="topScorer" class="label">Capocannoniere</label>
        <select name="topScorer"
                id="topScorer"
                class="select w-full bg-white dark:bg-neutral  @error('topScorer') border-error @enderror"
        >
            <option value="" @selected(null === old('topScorer', $prediction?->player_id))>{{ __('messages.champion.select_scorer') }}</option>
            @foreach($players as $player)
                <option
                        value="{{$player->id}}" @selected((int) old('topScorer', $prediction?->player_id) === $player->id)>
                    {{$player->displayed_name}} -
                    {{__($teams->where(static fn ($team) => $team->id === $player->national_id)->first()->name)}}
                </option>
            @endforeach
            @error('topScorer')
            <span class="text-error text-xs">{{ __('messages.common.required') }}</span>
            @enderror
        </select>
    </div>
    <button type="submit" class="btn btn-{{$btnTheme}} w-full">{{ $btnText }}</button>
</form>
