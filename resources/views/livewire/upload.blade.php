<div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Upload a Movie</h2>
    <div x-data="{ progress: 0, isUploading: false }">
        <input type="file" id="file" wire:model="file" class="mb-4 w-full border rounded-md" />
        <button 
            wire:click="uploadChunk" 
            class="bg-blue-500 text-white px-6 py-2 rounded-md"
            x-on:click="isUploading = true"
        >
            Upload
        </button>

        <div class="mt-4">
            <div x-show="isUploading" class="w-full bg-gray-200 h-2 rounded">
                <div 
                    class="h-2 bg-blue-500 rounded" 
                    :style="`width: ${progress}%`"
                ></div>
            </div>
            @if ($error)
                <p class="text-red-500 mt-2">{{ $error }}</p>
            @endif
            @if ($successMessage)
                <p class="text-green-500 mt-2">{{ $successMessage }}</p>
            @endif
        </div>
    </div>
</div>
