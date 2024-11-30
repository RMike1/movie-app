<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <h1 class="text-4xl text-white font-bold text-center mb-10">{{ $movie->movieName }}</h1>
        </h2>
            <a wire:navigate href="{{ route('dashboard') }}" class="text-white px-4 py-2 rounded-lg outline outline-offset-2 outline-gray-600/50 hover:bg-gray-600/50 mt-4">Back</a>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto px-4">
                    <div class="video-container">
                        <h3 class="text-xl text-white font-bold mt-4 mb-4">Watch Movie</h3>
                        <video controls class="w-full rounded-lg shadow-md pb-4">
                            <source src="{{ route('video.stream', ['filename' => basename($movie->path)]) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
