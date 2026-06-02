<form class="w-full flex flex-col justify-center items-center space-y-6" action="{{$action}}" method="POST">
    @csrf
    @method($method)
    <div class="w-full flex flex-col space-y-2 p-3">
        <label for="winner" class="label">
            Inserisci un pronostico per la squadra vincente dell'Europeo
        </label>
        @error('winner')
        <span class="text-error text-sm">Campo richiesto</span>
        @enderror
        <select name="winner"
                id="winner"
                class="select w-full bg-white @error('winner') border-error @enderror"
        >
            <option
                value=""
                @selected(null === old('winner', ($prediction ?? null)?->team->id))
            >-- Seleziona Squadra Vincente --</option>
            @foreach($teams as $team)
                <option
                    value="{{$team->id}}"
                    @selected(old('winner', ($prediction ?? null)?->team->id) === $team->id)>{{__($team->name)}}</option>
            @endforeach
        </select>
    </div>
    <div class="w-full flex flex-col space-y-2 p-3">
        <label for="topScorer" class="label">Inserisci un pronostico per il Capocannoniere dell'Europeo</label>
        @error('topScorer')
        <span class="text-error text-sm">Campo richiesto</span>
        @enderror
        <select name="topScorer"
                id="topScorer"
                class="select w-full bg-white  @error('topScorer') border-error @enderror"
        >
            <option value="" @selected(null === old('topScorer', ($prediction ?? null)?->player))>-- Seleziona Capocannoniere --</option>
            @foreach($players as $player)
                <option value="{{$player['id']}}" @selected(old('topScorer', ($prediction ?? null)?->player->id) === $player->id)>
                    {{$player->displayed_name}} -
                    {{__($teams->where(static fn ($team) => $team->id === $player->national_id)->first()->name)}}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn {{$btnBg}} text-base-100 fp2024-title w-full">{{$btnText}}</button>
</form>
