<div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/60 border border-base-300">
    <div class="size-9 rounded-lg bg-accent/15 flex items-center justify-center shrink-0 text-accent">
        {{ $slot }}
    </div>
    <div>
        <p class="text-xs text-base-content/50">{{ $label }}</p>
        <p class="text-sm font-semibold">{{ $value }}</p>
    </div>
    @if($isPredictionSuccessful ?? false)
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-auto size-5 text-success">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
    @endif
</div>
