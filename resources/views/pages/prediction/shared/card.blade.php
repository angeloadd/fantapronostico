<div class="size-full flex flex-col items-center justify-start">
    <div
            @class([
            'md:card min-w-0 md:shadow-lg md:rounded-2xl md:border md:bg-base-100',
            'md:border-accent' => ($status ?? null) === null,
            'md:border-base-300' => ($status ?? null) === 'disabled',
            'md:border-primary' => ($status ?? null) === 'edit',
            'w-full lg:max-w-xl' => $isForm ?? false,
            'w-full lg:max-w-6/8' => !($isForm ?? false)
    ])>
        <div class="card-body">
            {{$slot}}
        </div>
    </div>
</div>
