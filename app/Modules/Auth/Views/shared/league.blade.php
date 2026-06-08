<div class="flex-1 flex flex-col items-center justify-center gap-4 w-full">
    <h2 class="text-2xl">{{__('auth.league.heading')}}</h2>
    <x-auth::shared.form
            action="{{route('leagues.subscribe')}}"
            method="POST"
            :formControls="[]"
            btnText="{{__('auth.league.subscribe')}}"
            hint="{{__('auth.league.hint')}}"
    >
        <select class="select w-full" name="league_id" id="league_id">
            @foreach($leagues as $league)
                <option value="{{$league->id}}">{{$league->name}}</option>
            @endforeach
        </select>
    </x-auth::shared.form>
</div>
