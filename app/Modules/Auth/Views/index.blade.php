<x-layouts.app>
    <x-slot name="title">{{__('auth.'. str_replace('-', '_',$pageName) .'.title')}}</x-slot>
    <main class="flex items-center justify-center min-h-screen">
        <aside class="hidden lg:block lg:basis-3/5 xl:basis-1/2 overflow-hidden self-stretch">
            <img
                    class="object-cover object-center w-full h-full xl:object-top"
                    src="{{Vite::asset('resources/assets/images/football_player.png')}}"
                    alt="Draw of a football player cheering with a cup"
            >
        </aside>
        <section class="w-full flex flex-col justify-center items-center min-h-screen px-6 lg:basis-2/5 xl:basis-1/2">
            <div class="card bg-base-100 shadow-lg w-full max-w-sm min-h-115">
                <div class="card-body">
                    <x-partials.logo.large primary="var(--color-primary)" secondary="var(--color-accent)"/>
                    <div class="flex-1 flex flex-col w-full">
                        <x-dynamic-component :component="'auth::shared.'.$pageName" :leagues="$leagues ?? collect([])"/>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
