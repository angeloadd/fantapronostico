<div class="flex justify-center items-center space-x-2">
    <input
        id="{{$label}}"
        type="radio"
        value="{{$value}}"
        name="sign"
        class="radio bg-white dark:bg-neutral mx-2 @error('sign') border-error @enderror"
        @checked(old('sign', $prediction?->sign) === $value)
    />
    <label for="{{$label}}" class="text-sm text-center">{{strtoupper($value)}}: {{__($teamName)}}</label>
</div>
