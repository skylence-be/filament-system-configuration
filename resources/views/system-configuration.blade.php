<div class="space-y-6">
    @if($currentSection)
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-check">
                    {{ config('filament-system-configuration.actions.save.label', 'Save Configuration') }}
                </x-filament::button>
            </div>
        </form>
    @else
        <x-filament::section>
            <x-slot name="heading">
                Configuration Not Found
            </x-slot>
            <p class="text-gray-500 dark:text-gray-400">
                The requested configuration section was not found. Please select a section from the sidebar.
            </p>
        </x-filament::section>
    @endif
</div>
