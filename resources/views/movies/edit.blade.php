<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
            <h1 class="text-4xl text-white font-bold text-center mb-10">Movie App</h1>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto p-6 bg-gray-900 shadow-md rounded-lg mt-10">
                <h2 class="text-2xl text-white font-bold mb-4">Edit Movie: {{ $movie->name }}</h2>
            
                <form action="{{ route('movies.update', $movie->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
            
                    <div class="mb-4">
                        <label for="name" class="text-white">Movie Name</label>
                        <input type="text" id="name" name="name" class="w-full p-2 mt-2 bg-gray-700  rounded-md" value="{{ old('name', $movie->name) }}" required>
                    </div>
            
                    <div class="mb-4">
                        <label for="file" class="text-white">Upload New Movie File</label>
                        <input type="file" id="file" name="file" class="w-full p-2 mt-2 bg-gray-700 text-white rounded-md">
                    </div>
            
                    <button type="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded-md">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
