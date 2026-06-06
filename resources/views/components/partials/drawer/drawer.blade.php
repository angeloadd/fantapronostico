<div class="drawer lg:drawer-open w-full">
    <x-partials.drawer.content>
        {{$slot}}
    </x-partials.drawer.content>
    <x-partials.drawer.side/>
</div>
<x-auth::logout/>
