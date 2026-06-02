<div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/60 border border-base-300">
    <div class="size-9 rounded-lg bg-accent/15 flex items-center justify-center shrink-0 text-accent">
        {{ $slot }}
    </div>
    <div>
        <p class="text-xs text-base-content/50">{{ $label }}</p>
        <p class="text-sm font-semibold">{{ $value }}</p>
    </div>
</div>