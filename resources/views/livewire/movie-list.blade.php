<div class="max-w-7xl mx-auto p-6 bg-gray-900 mb-4 shadow-md rounded-lg mt-10">
    <h2 class="text-2xl justify-content text-white font-bold mb-4">Recent Movies</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($movies as $movie)
        <div class="p-4 bg-gray-800 rounded-lg shadow-2xl" wire:key="{{$movie->id}}">
            <h3 class="text-lg text-slate-200 font-semibold">{{ $movie->movieName }}</h3>
            <a wire:navigate href="{{ route('movie.show', $movie->slug) }}">
                <video class="w-full mt-2 rounded-md shadow-md">
                    <source src="{{ route('video.stream', ['filename' => basename($movie->path)]) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </a>
            <button type="submit" wire:confirm="sure to delete this movie?" wire:click="deleteMovie({{$movie->id}})" class="mt-3 text-red-500 border border-red-600/50 p-2 rounded-sm hover:text-red-700">Delete</button>
        </div>
    @empty
        <p class="text-gray-500">No movies available.</p>
    @endforelse
</div>
</div>
