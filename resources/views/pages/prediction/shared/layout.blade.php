<x-layouts.with-drawer>
@if($errors->any())
    @foreach($errors->all() as $error)
        <x-partials.notifications.toast-message :validation="$error"/>
    @endforeach
@endif
    <div class="size-full flex flex-col items-center justify-start">
        {{$slot}}
    </div>
</x-layouts.with-drawer>
