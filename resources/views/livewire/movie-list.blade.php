<div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg mt-10">
    <h2 class="text-2xl font-bold mb-4">Available Movies</h2>
    <div class="space-y-6">
        @forelse ($movies as $movie)
            <div class="p-4 bg-gray-100 rounded-lg">
                <h3 class="text-lg font-semibold">{{ $movie->name }}</h3>
                <video controls class="w-full mt-2 rounded-md shadow">
                    <source src="{{ Storage::disk('public')->url($movie->path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @empty
            <p class="text-gray-500">No movies available.</p>
        @endforelse
    </div>
</div>
