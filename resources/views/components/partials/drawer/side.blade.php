<aside class="drawer-side">
    <label for="sidebarBtn" aria-label="close sidebar" class="drawer-overlay"></label>
    <div class="min-h-screen bg-primary flex flex-col items-center justify-start" id="sideBar">
        <div class="h-14 flex justify-center items-center pt-1">
            <x-partials.logo.large primary="var(--color-primary-content)" secondary="var(--color-accent)" width="w-60"/>
        </div>
        <div class="divider divider-neutral m-0"></div>

        <ul class="menu text-lg w-full mb-4 space-y-1">
            <x-partials.drawer.item routeName="home" svg="home" text="Dashboard"/>
            <x-partials.drawer.item routeName="prediction.next-from-ref" active="prediction" svg="bet" text="Pronostico"/>
            <x-partials.drawer.item routeName="champion.create" active="champion" svg="winner" text="Vincente"/>
        </ul>
        <p class="text-primary-content/50 text-left w-full px-5 pt-5 text-sm">ESPLORA</p>
        <ul class="menu text-lg w-full mb-4 space-y-1">
            <x-partials.drawer.item routeName="standing" svg="rank" text="Classifica"/>
            <x-partials.drawer.item routeName="albo" svg="albo" text="Albo d'oro"/>
            <x-partials.drawer.item routeName="terms" svg="terms" text="Regolamento"/>
        </ul>

        @if(auth()?->user()?->mod)
            <p class="text-primary-content/50 text-left w-full px-5 pt-5 text-sm">ADMIN</p>
            <ul class="menu text-lg w-full">
                <li>
                    <a href="/admin"
                       class="text-primary-content/85 rounded-2xl hover:bg-[#2b3b5a]"
                    >
                        <x-dynamic-component :component="'partials.svgs.admin'"/>
                        <span class="pl-1">Pannello Mod</span>
                    </a>
                </li>
            </ul>
        @endif
    </div>
</aside>
