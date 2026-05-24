@if(!empty($hidden ?? null))
    <input
        id="{{$name}}"
        name="{{$name}}"
        type="{{$type}}"
        class="hidden"
        value="{{old($name, $value ?? null)}}"
    />
@else
    <div class="flex flex-col gap-1">
        <label for="{{$name}}" class="@if(!isset($label)) hidden @endif text-base-content/70">{{$label ?? ''}}</label>
        <input
            id="{{$name}}"
            name="{{$name}}"
            type="{{$type}}"
            placeholder="{{!empty($placeholder ?? null) ? $placeholder : null}}"
            class="input w-full border-base-content/20 @error($name) border-error @enderror"
            @checked(!empty($checked ?? null) && $type === 'checkbox')
            @if('password' !== $type) value="{{old($name, $value ?? null)}}" @endif
        />
        @error($name)
            @foreach($errors->get($name) as $error)
                <span class="text-error text-xs mt-1">{{$error}}</span>
            @endforeach
        @enderror
    </div>
@endif
