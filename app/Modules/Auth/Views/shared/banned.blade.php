<div hx-get="{{ route('leagues.check') }}" hx-trigger="every 3s" hx-swap="none"></div>

<div class="flex-1 flex flex-col items-center justify-center gap-4 w-full text-center">
    <svg style="stroke: var(--color-accent)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-16">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>

    <h2 class="text-2xl">
        {{__('auth.banned.heading')}}
    </h2>
    <span>{{__('auth.banned.paragraph')}}</span>
    <div class="tooltip tooltip-success"
         x-data="{ copied: false }"
         :data-tip="copied ? 'e-mail copiata' : ''"
         :class="copied ? 'tooltip-open' : ''"
         @click="navigator.clipboard.writeText('cicciofrizzo91@gmail.com'); copied = true"
    >
        <button
                class="btn btn-base font-normal rounded-full border-info text-info"
                :class="copied ? 'border-success text-success' : 'border-info text-info hover:border-primary hover:text-primary'"
        >
            Copia e-mail
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6">
                <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                <path d="m9 14 2 2 4-4"/>
            </svg>
        </button>
    </div>
</div>
