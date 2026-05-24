<form action="{{$action}}" method="{{$method}}" class="w-full">
    @method($method)
    @csrf
    <div class="flex flex-col gap-4 w-full">
        @isset($slot) {{$slot}} @endisset
        @foreach($formControls as $formControl)
            <x-auth::shared.form-control
                    :name="$formControl['name']"
                    :type="$formControl['type']"
                    :placeholder="!empty($formControl['placeholder'] ?? null) ? $formControl['placeholder'] : null"
                    :prefix="$prefix"
                    :value="$formControl['value'] ?? null"
                    :checked="!empty($formControl['checked'] ?? null)"
                    :hidden="!empty($formControl['hidden'])"
            />
        @endforeach

        @if($passwordReset ?? false)
            <a
                    href="{{route('password.email')}}"
                    class="text text-right text-base-content/70 hover:text-primary hover:underline"
            >{{__('auth.login.request_password_reset')}}</a>
        @endif

        @if(isset($hint))
            <span class="text-base-content/70 text-sm">
                {{ $hint }}
            </span>
        @endif

        @if(isset($btnText) && ! isset($btn))
            <button class="btn btn-accent">{{$btnText}}</button>
        @else
            {{$btn}}
        @endif
    </div>
</form>
