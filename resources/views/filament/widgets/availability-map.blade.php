<x-filament-widgets::widget>
    <x-filament::section heading="Availability checks" :description="count($points) . ' on the map' . ($unlocatedCount ? ', ' . $unlocatedCount . ' not located' : '')">
        {{-- Started by availabilityMap() in resources/js/admin-map.js. wire:ignore keeps Livewire from
             re-rendering Leaflet's DOM. Sized with inline styles: Filament's prebuilt CSS doesn't
             include Tailwind classes that only this view uses. --}}
        <div
            wire:ignore
            x-data="availabilityMap(@js($points))"
            style="height: 28rem; width: 100%; overflow: hidden; border-radius: 0.5rem; z-index: 0"
            role="region"
            aria-label="Map of availability check addresses"
        ></div>
    </x-filament::section>
</x-filament-widgets::widget>
