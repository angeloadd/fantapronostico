<div class="size-full flex flex-col items-center justify-start">
    <div
            @class([
            'card min-w-0 shadow-lg rounded-2xl border bg-base-100',
            'border-accent' => ($status ?? null) === null,
            'border-base-300' => ($status ?? null) === 'disabled',
            'border-primary' => ($status ?? null) === 'edit',
            'w-full md:max-w-xl' => $isForm ?? false,
            'w-full md:w-6/8' => !($isForm ?? false)
    ])>
        <div class="card-body">
            {{$slot}}
        </div>
    </div>
</div>
