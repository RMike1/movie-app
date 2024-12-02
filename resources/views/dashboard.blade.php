<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <h1 class="text-4xl text-white font-bold text-center mb-10">Movie App</h1>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto px-4">
                    <livewire:upload />
                    <livewire:movie-list lazy/>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
