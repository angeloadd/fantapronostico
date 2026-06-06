<input class="drawer-toggle" type="checkbox" id="sidebarBtn"/>
<div class="drawer-content flex flex-col min-h-screen md:h-screen">
    <x-partials.drawer.navbar/>
    <div class="flex-1 md:overflow-auto p-4 md:p-6">
        {{$slot}}
    </div>
</div>
