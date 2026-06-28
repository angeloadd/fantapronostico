<x-filament-widgets::widget class="fi-wi-insert-prediction">
    <x-filament::section heading="Inserisci pronostico">
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    Inserisci pronostico
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>