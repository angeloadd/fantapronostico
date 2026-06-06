<div class="sticky z-10 top-0 lg:static w-full navbar bg-base-100 flex justify-between items-center shadow-lg h-14">
    <div class="navbar-start lg:hidden">
        <label for="sidebarBtn" aria-label="{{ __('messages.accessibility.close_sidebar') }}" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5">
                <rect width="18" height="18" x="3" y="3" rx="2"/>
                <path d="M9 3v18"/>
            </svg>
        </label>
    </div>
    <div class="navbar-center md:hidden">
        <x-partials.logo.ball width="w-60"/>
    </div>
    <div class="navbar-end md:ml-auto gap-3 sm:gap-6">
        <span class="hidden md:inline text-base-content/65">
            {{Auth::user()->name ?? __('messages.common.hello')}}
        </span>

        <x-partials.themes.toggle/>

        <button class="text-base-content mr-4" type="button" onclick="logOutModal.showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cursor-pointer size-5 md:size-6">
                <path d="m16 17 5-5-5-5"/>
                <path d="M21 12H9"/>
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            </svg>
        </button>
    </div>
</div>
