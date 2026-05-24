<x-partials.notifications.toast-message />

<div class="flex-1 flex flex-col items-center justify-center gap-4 w-full">
    <h2 class="text-2xl">Inscriviti ad una lega</h2>
    <form action="{{route('leagues.subscribe')}}" method="POST" class="flex flex-col gap-4 w-full">
        @csrf
        <label for="league_id" class="text-sm text-base-content/70">
            Scegli una delle leghe presenti ed iscriviti.
        </label>
        <select class="select w-full" name="league_id" id="league_id">
            @foreach($leagues as $league)
                <option value="{{$league->id}}">{{$league->name}}</option>
            @endforeach
        </select>
        <button class="btn btn-primary w-full">Richiedi Iscrizione</button>
    </form>
</div>
