<div class="flex justify-center items-center space-x-2">
    <input
        id="{{$label}}"
        type="radio"
        value="{{$value}}"
        name="sign"
        class="radio bg-white mx-2 @error('sign') border-error @enderror"
        @checked(old('sign', $prediction?->sign) === $value)
    />
    <label for="{{$label}}">{{strtoupper($value)}}: {{__($teamName)}}</label>
</div>
