<div class="p-2 sm:p-8">
    <x-prediction::shared.card :status="$status ?? null">
        {{$slot}}
    </x-prediction::shared.card>
</div>
