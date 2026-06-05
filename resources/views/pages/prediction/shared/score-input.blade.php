<label for="{{$label}}" @class([
    'label text-base-content',
    'order-last' => $label === 'away_score',
])>
    <span class="md:hidden text-sm">{{__($label)}}</span>
    <span class="hidden md:inline">
       {{ __('messages.prediction.result') }} {{__($teamName)}}
    </span>
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
