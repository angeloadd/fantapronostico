<div class="flex w-full justify-center items-center">
    <label class="label hidden" for="{{$label}}"></label>
    <select
        name="{{$label}}"
        id="{{$label}}"
        class="select select-md w-full bg-white text-lg @error($label) border-error @enderror"
    >
        <option
            value=""
            @selected(null === old($label, $prediction?->{$label}))
        >-- Seleziona Gol {{__($teamName)}} --</option>
        @foreach($players as $id => $player)
            <option
                value="{{$id}}"
                @selected(old($label, $prediction?->{$label}) === (string) $id)
            >{{$player}}</option>
        @endforeach
    </select>
</div>
