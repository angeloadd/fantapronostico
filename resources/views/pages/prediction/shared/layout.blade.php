<x-layouts.with-drawer>
    @if($errors->any())
        @foreach($errors->all() as $error)
            <x-partials.notifications.toast-message :validation="$error"/>
        @endforeach
    @endif
    {{$slot}}
</x-layouts.with-drawer>
