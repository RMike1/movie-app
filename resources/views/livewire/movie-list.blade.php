<div class="max-w-7xl mx-auto p-6 bg-gray-900 mb-4 shadow-md rounded-lg mt-10">
    <h2 class="text-2xl justify-content text-white font-bold mb-4">Recent Movies</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2  lg:grid-cols-3 gap-6">
        @forelse ($movies as $movie)
            <div class="p-4 bg-gray-800 rounded-lg shadow-2xl">
                <h3 class="text-lg text-slate-200 font-semibold">{{ $movie->movieName }}</h3>
                <video controls class="w-full mt-2 rounded-md shadow-md">
                    <source src="{{ Storage::url($movie->path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <form action="{{ route('movies.destroy', $movie->id) }}" method="POST" onsubmit="return confirm('sure to delete this movie?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="mt-3 text-red-500 hover:text-red-700">Delete</button>
                </form>
            </div>
        @empty
            <p class="text-gray-500">No movies available.</p>
        @endforelse
    </div>
</div>
