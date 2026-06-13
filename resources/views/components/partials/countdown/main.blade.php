<div
        @class([
        'flex items-center justify-center gap-2 badge rounded-full',
        'bg-accent/30 border-accent text-accent' => ($isOpen ?? true) && !($isExpired ?? false),
        'bg-base-300 text-base-content/50' => !($isOpen ?? true) || ($isExpired ?? false),
])>
    @if($isExpired ?? false)
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3">
            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        {{ __('messages.countdown.expired') }}
    @else
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 6v6l4 2"/>
        </svg>
        <div
                x-cloak
                x-data="countdown({{$date->avoidMutation()->timezone(Auth::user()?->timezone ?? 'Europe/Rome')->timestamp * 1000}})"
                class="countdown"
        >
            {{$isOpen ?? true ? __('messages.countdown.closes') : __('messages.countdown.opens')}}&nbsp;{{ __('messages.countdown.in') }}&nbsp;
            <span x-bind:style="{ '--value': days }" aria-live="polite" x-bind:aria-label="days"></span>g&nbsp;
            <span x-bind:style="{ '--value': hours }" aria-live="polite" x-bind:aria-label="hours"></span>o&nbsp;
            <span x-bind:style="{ '--value': minutes }" aria-live="polite" x-bind:aria-label="minutes"></span>m&nbsp;
            <span x-bind:style="{ '--value': seconds }" aria-live="polite" x-bind:aria-label="seconds"></span>s
        </div>
    @endif
</div>
