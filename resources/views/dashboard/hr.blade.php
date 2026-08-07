<x-app-layout title="HR Dashboard">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Panel de Control RRHH') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-2 sm:px-4 lg:px-6 w-full">
        <livewire:dashboard.hr-dashboard />
    </div>
</x-app-layout>