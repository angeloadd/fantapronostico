<div
        @class([
        'card w-full shadow-lg rounded-2xl border bg-base-100',
        'border-accent' => ($status ?? null) === null,
        'border-base-300' => ($status ?? null) === 'disabled',
        'border-primary' => ($status ?? null) === 'edit',
])>
    <div class="card-body p-2 sm:p-8">
        {{$slot}}
    </div>
</div>
