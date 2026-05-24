<form action="{{$action}}" method="{{$method}}" class="w-full">
    @method($method)
    @csrf
    <div class="flex flex-col gap-4">
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
        @if(isset($btnText) && ! isset($btn))
            <div class="flex flex-col mt-2">
                @if($passwordReset ?? false)
                    <a
                            href="{{route('password.email')}}"
                            class="text-xs text-right text-base-content/50 hover:text-primary pb-2"
                    >{{__('auth.login.request_password_reset')}}</a>
                @endif
                @if(isset($explanation))
                    <span class="text-base-content/50 text-xs mb-2">
                        {{ $explanation }}
                    </span>
                @endif
                <button class="btn btn-accent text-accent-content fp2024-title">{{$btnText}}</button>
            </div>
        @else
            {{$btn}}
        @endif
    </div>
</form>
