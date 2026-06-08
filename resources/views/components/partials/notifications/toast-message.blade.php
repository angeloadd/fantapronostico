@if(!isset($validation))
    @if(!isset($sessionKey))
        @foreach([
        'message' => 'info',
        'error_message' => 'error',
        'warning_message' => 'warning',
        'info_message' => 'info',
        'status' => 'info',
    ] as $key => $alertType)
            @if(null !== session($key))
                <template x-teleport="#toastWrapper">
                    <x-partials.notifications.toast :text="session($key)" type="{{$alertType}}"/>
                </template>
            @endif
        @endforeach
    @else
        <template x-teleport="#toastWrapper">
            <x-partials.notifications.toast :text="session($sessionKey)" type="{{$type ?? 'info'}}"/>
        </template>
    @endif
@else
    <template x-teleport="#toastWrapper">
        <x-partials.notifications.toast :text="$validation" type="error"/>
    </template>
@endif
