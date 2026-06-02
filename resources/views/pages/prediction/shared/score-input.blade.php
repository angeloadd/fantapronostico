<label for="{{$label}}" @class([
    'label',
    'order-last' => $label === 'away_score',
])>
    Risultato {{__($teamName)}}
</label>
<input
    type="number"
    min="0"
    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
    name="{{$label}}"
    class="input bg-white mx-2 input-sm text-lg w-16 @error($label) border-error @enderror"
    id="{{$label}}"
    value="{{old($label, $prediction?->{$label})}}"
>
