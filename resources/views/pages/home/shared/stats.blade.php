<div class="flex flex-col gap-2 h-full">
    <h2 class="shrink-0 text-xs font-medium text-base-content/50 uppercase tracking-wider">Statistiche</h2>
    <div class="flex-1 min-h-0">
        <x-home::shared.card>
            <div class="grid grid-cols-2 gird-rows-2 gap-6 my-auto">
                <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/60 border border-base-300">
                    <div class="w-9 h-9 rounded-lg bg-accent/15 flex items-center justify-center shrink-0">
                        <img src="{{ Vite::asset('resources/assets/images/winner.svg') }}" class="w-5 h-5" alt="trophy">
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">PUNTI</p>
                        <p class="text-sm font-semibold">{{$userRank->total ?? 0}}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/60 border border-base-300">
                    <div class="w-9 h-9 rounded-lg bg-accent/15 flex items-center justify-center shrink-0">
                        <img src="{{ Vite::asset('resources/assets/images/golden_boot.svg') }}" class="w-5 h-5" alt="golden boot">
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">ESATTI</p>
                        <p class="text-sm font-semibold">{{$userRank->numberOfResults ?? 0}}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/60 border border-base-300">
                    <div class="w-9 h-9 rounded-lg bg-accent/15 flex items-center justify-center shrink-0">
                        <img src="{{ Vite::asset('resources/assets/images/winner.svg') }}" class="w-5 h-5" alt="trophy">
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">SEGNI</p>
                        <p class="text-sm font-semibold">{{$userRank->numberOfSigns ?? 0}}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/60 border border-base-300">
                    <div class="w-9 h-9 rounded-lg bg-accent/15 flex items-center justify-center shrink-0">
                        <img src="{{ Vite::asset('resources/assets/images/golden_boot.svg') }}" class="w-5 h-5" alt="golden boot">
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">GOL</p>
                        <p class="text-sm font-semibold">{{$userRank->numberOfScorers ?? 0}}</p>
                    </div>
                </div>
            </div>
        </x-home::shared.card>
    </div>
</div>
