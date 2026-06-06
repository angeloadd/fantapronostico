<div class="flex flex-col gap-2 h-full flex-1 min-h-0">
    @if(!empty($title ?? null))
        <x-shared.card-title>{{ $title }}</x-shared.card-title>
    @endif
    <div @class([
        "card flex-1 min-h-0 w-full shadow-lg rounded-2xl border bg-base-100",
        "border-accent/30" => !($grayed ?? false),
        'border-base-300' => ($grayed ?? false),
        ])>
        <div class="card-body {{$overflow ?? ''}} p-4 md:p-5">
            {{ $slot }}
        </div>
    </div>
</div>
