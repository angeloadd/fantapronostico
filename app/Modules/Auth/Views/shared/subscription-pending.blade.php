<x-partials.notifications.toast-message sessionKey="status"/>

<div hx-get="{{ route('leagues.check') }}" hx-trigger="every 3s" hx-swap="none"></div>

<div class="flex-1 flex flex-col items-center justify-center gap-4 w-full text-center">
    <svg style="stroke: var(--color-accent)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-16">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>

    <h2 class="text-2xl">
        {{__('auth.subscription_pending.heading')}}
    </h2>
    <span class="">
        {{__('auth.subscription_pending.paragraph')}}
    </span>
</div>
