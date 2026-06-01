<div @class([
        "card h-full w-full shadow-lg rounded-2xl border bg-base-100",
        "border-accent/30" => !($grayed ?? false),
        'border-base-300' => ($grayed ?? false),
        ])>
    <div class="card-body overflow-auto p-4 md:p-5">
        {{$slot}}
    </div>
</div>
