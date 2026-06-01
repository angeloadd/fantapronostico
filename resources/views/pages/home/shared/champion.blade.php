<div class="flex flex-col gap-2 h-full">
    <h2 class="shrink-0 text-xs font-medium text-base-content/50 uppercase tracking-wider">Pronostico Vincente</h2>
    <div class="flex-1 min-h-0">
        <x-home::shared.card :grayed="empty($champion ?? null) && $hasTournamentStarted">
            <div class="my-auto flex flex-col gap-4">
                @if(!empty($champion ?? null))
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/60 border border-base-300">
                        <div class="size-9 rounded-lg bg-accent/15 flex items-center justify-center shrink-0 text-accent">
                            <x-partials.svgs.rank/>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50">Vincitore</p>
                            <p class="text-sm font-semibold">{{ __($champion->team->name) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/60 border border-base-300">
                        <div class="size-9 rounded-lg bg-accent/15 flex items-center justify-center shrink-0 text-accent">
                            <x-partials.svgs.boot/>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50">Capocannoniere</p>
                            <p class="text-sm font-semibold">{{ $champion->player->displayed_name }}</p>
                        </div>
                    </div>
                @endif

                @if(empty($champion ?? null) && $hasTournamentStarted)
                    <div class="ml-auto flex items-center gap-2 badge bg-base-300 rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Bloccato
                    </div>
                    <div class="flex-1 flex flex-col items-center justify-center gap-4">
                        <div class="bg-base-300 size-12 rounded-full flex items-center justify-center text-base-content/50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <span class="font-bold text-base-content/60">Vincente e Capocannoniere non selezionati</span>
                        <span class="text-center">Impossibile selezionare pronostico vincente e capocannoniere dopo l'inizio del torneo</span>
                    </div>
                @elseif(empty($champion ?? null))
                    <div class="flex-1 flex flex-col items-center justify-center gap-2 text-center">
                        <div class="ml-auto flex items-center gap-2 badge bg-accent/30 border-accent rounded-full mb-4 text-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            <span x-data="{
                                startDate: new Date('{{ $tournamentStartedAt }}'.replace(' ', 'T')).getTime(),
                                countdown: 0,
                                days: 0, hours: 0, minutes: 0, seconds: 0,
                                setDiff() {
                                    this.countdown = this.startDate - new Date().getTime()
                                    this.days    = Math.floor(this.countdown / (1000 * 60 * 60 * 24))
                                    this.hours   = Math.floor((this.countdown % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
                                    this.minutes = Math.floor((this.countdown % (1000 * 60 * 60)) / (1000 * 60))
                                    this.seconds = Math.floor((this.countdown % (1000 * 60)) / 1000)
                                },
                                init() {
                                    this.setDiff()
                                    let x = setInterval(() => {
                                        this.setDiff()
                                        if (this.countdown < 0) { clearInterval(x); this.countdown = 0 }
                                    }, 1000)
                                }
                            }" class="countdown">
                                Chiude in&nbsp;<span x-bind:style="{ '--value': days }" aria-live="polite" x-bind:aria-label="days"></span>d&nbsp;
                                <span x-bind:style="{ '--value': hours }" aria-live="polite" x-bind:aria-label="hours"></span>h&nbsp;
                                <span x-bind:style="{ '--value': minutes }" aria-live="polite" x-bind:aria-label="minutes"></span>m&nbsp;
                                <span x-bind:style="{ '--value': seconds }" aria-live="polite" x-bind:aria-label="seconds"></span>s
                            </span>
                        </div>
                        <div class="m-auto bg-accent/30 text-accent size-12 rounded-full flex items-center justify-center">
                            <x-partials.svgs.rank/>
                            <x-partials.svgs.boot/>
                        </div>
                        <span class="font-bold text-base-content/60">Selezione Vincente e Capocannoniere</span>
                        <a href="{{ route('champion.index') }}" class="btn btn-primary btn-lg rounded-2xl w-full mt-auto shrink-0">
                            Crea Pronostico
                        </a>
                    </div>
                @elseif(!$hasTournamentStarted)
                    <a href="{{ route('champion.index') }}" class="btn btn-primary btn-lg rounded-2xl w-full mt-auto shrink-0">
                        Modifica Pronostico
                    </a>
                @endif
            </div>
        </x-home::shared.card>
    </div>
</div>