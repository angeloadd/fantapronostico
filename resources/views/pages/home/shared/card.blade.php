<div class="card flex-1 min-h-0 size-full gap-2">
    @if(!empty($title ?? null))
        <x-shared.card-title>{{ $title }}</x-shared.card-title>
    @endif
    <div
            @class([
                'card-body shadow-lg rounded-2xl border bg-base-100 overflow-auto min-h-0 grow p-4 md:p-5 flex flex-col my-auto',
                "border-accent/30" => !($grayed ?? false),
                'border-base-300' => ($grayed ?? false),
                ])>
        {{ $slot }}
    </div>
</div>
