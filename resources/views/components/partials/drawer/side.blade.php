<aside class="drawer-side">
    <label for="sidebarBtn" aria-label="close sidebar" class="drawer-overlay"></label>
    <div class="min-h-screen bg-secondary flex flex-col items-center justify-between text-secondary-content" id="sideBar">
        <ul class="menu text-lg space-y-3">
            <li class="w-72 mb-9">
                <x-partials.logo.large primary="#e5e7eb"/>
            </li>
            <x-partials.drawer.item routeName="home" svg="home" text="Home"/>
            <x-partials.drawer.item routeName="prediction.next-from-ref" active="prediction" svg="bet" text="Pronostico"/>
            <x-partials.drawer.item routeName="champion.create" active="champion" svg="winner" text="Vincente"/>
            <x-partials.drawer.item routeName="standing" svg="rank" text="Classifica"/>
            <x-partials.drawer.item routeName="albo" svg="albo" text="Albo d'oro"/>
            <x-partials.drawer.item routeName="terms" svg="terms" text="Regolamento"/>
        </ul>
        <ul class="menu w-full">
            <li class="w-full">
                <div class="bg-base-100/70 p-[0.025rem] -mt-3 mx-3"></div>
            </li>
            <li>
                <div class="dropdown dropdown-top dropdown-start flex hover:bg-neutral/30">
                    <div tabindex="0" role="button" class=" flex items-center w-full">
                        <img src="{{Vite::asset('resources/assets/images/profile.svg')}}"
                             alt="grind"
                             width="32"
                             class="rounded-circle me-2">
                        <p><strong class="text-2xl">{{Auth::user()->name ?? 'Accedi'}}</strong></p>
                    </div>
                    <ul tabindex="0" class="dropdown-content z-[1] text-lg rounded-lg bg-accent shadow-lg" aria-labelledby="dropdownUser1">
                        @guest
                            <li>
                                <a href="{{ route('login') }}">Accedi</a>
                            </li>
                            <li>
                                <a href="{{ route('register') }}">Iscriviti</a>
                            </li>
                        @else
                            @if(auth()->user()->admin)
                                <li>
                                    <a href="/admin">
                                        PannelloMod
                                    </a>
                                </li>
                            @endif
                            <li>
                                <button type="button" onclick="logOutModal.showModal()">
                                    Logout
                                </button>
                            </li>
                        @endguest
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</aside>
