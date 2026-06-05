<label for="{{$label}}" @class([
    'label text-base-content',
    'order-last' => $label === 'away_score',
])>
    {{ __('messages.prediction.result') }} {{__($teamName)}}
</label>
<input
    type="number"
    min="0"
    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
    name="{{$label}}"
    class="input bg-white dark:bg-neutral mx-2 input-sm text-lg w-16 @error($label) border-error @enderror"
    id="{{$label}}"
    value="{{old($label, $prediction?->{$label})}}"
>
