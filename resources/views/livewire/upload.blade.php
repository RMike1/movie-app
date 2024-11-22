<div class="max-w-3xl mx-auto mt-4 p-6 bg-gray-900 shadow-md rounded-lg">
    <h2 class="text-2xl text-white font-bold mb-4">Upload a Movie</h2>

    <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"
        x-on:livewire-upload-finish="isUploading = false; progress = 0;"
        x-on:livewire-upload-progress="progress = $event.detail.progress" class="space-y-4">

        <input type="text" placeholder="Movie name..." id="movieName" wire:model="movieName"
            class="w-full p-3 bg-gray-800 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" />
        @error('movieName')
            <p class="text-red-500">{{ $message }}</p>
        @enderror

        <input type="file" id="file" wire:model="file"
            class="w-full p-3 bg-gray-800 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" />
        @error('file')
            <p class="text-red-500">{{ $message }}</p>
        @enderror

        <button wire:click="uploadChunk"
            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-md"
            x-on:click="isUploading = true">
            Upload
        </button>

        <div x-show="isUploading" class="mt-4">
            <div class="w-full bg-gray-800 rounded-full h-2">
                <div class="bg-gray-600 h-2 rounded-full transition-all duration-300" :style="`width: ${progress}%`">
                </div>
            </div>
            <p class="text-white mt-2 text-sm" x-text="`${progress}% Uploading...`"></p>
            <button type="button" class="text-sm text-red-500" x-on:click="isUploading = false" wire:click="$cancelUpload('file')">Cancel</button>
        </div>

        @if ($error)
            <p class="text-red-500 mt-2">{{ $error }}</p>
        @endif
        @if ($successMessage)
            <p class="text-green-500 mt-2">{{ $successMessage }}</p>
        @endif
    </div>
</div>
