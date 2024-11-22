<div class="max-w-3xl mx-auto mt-4 p-6 bg-gray-900 shadow-md rounded-lg">
    <h2 class="text-2xl text-white text-center font-bold mb-4">Upload Movie</h2>
    <div 
        x-data="{
            isUploading: false,
            progress: 0,
            videoPath: '',
            uploadButton: false,
            resumable: null,

            init() {
                const self = this;
                this.resumable = new Resumable({
                    target: '{{ route('files.upload.large') }}',
                    query: { _token: '{{ csrf_token() }}' },
                    fileType: ['mp4'],
                    chunkSize: 10 * 1024 * 1024,
                    testChunks: false,
                });

                this.resumable.assignBrowse(this.$refs.fileInput);

                this.resumable.on('fileAdded', function() {
                    self.isUploading = true;
                    self.resumable.upload();
                });

                this.resumable.on('fileProgress', function(file) {
                    self.progress = Math.floor(file.progress() * 100);
                });

                this.resumable.on('fileSuccess', function(file, response) {
                    const data = JSON.parse(response);
                    self.videoPath = data.path;
                    self.isUploading = false;
                    self.uploadButton = true;
                });

                this.resumable.on('fileError', function() {
                    alert('File uploading error.');
                    self.isUploading = false;
                });
            }
        }" 
        class="space-y-8"
    >
        <div class="text-center flex">
            <input type="text" placeholder="Movie name..." id="movieName" wire:model="movieName"
                class="p-3 flex-grow bg-gray-800 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" />
            @error('movieName')
                <p class="text-red-500">{{ $message }}</p>
            @enderror

            <button @click="$refs.fileInput.click()"
                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                Browse Movie
            </button>
            <input type="file" wire:model="fileImage" class="hidden" x-ref="fileInput" />

            @error('file')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div x-show="isUploading" class="w-full bg-gray-200 rounded-lg h-6">
            <div class="bg-gray-500 h-full rounded-lg text-xs text-white text-center" :style="{ width: `${progress}%` }"
                x-text="`${progress}%`">
            </div>
            <button
                class="text-red-500 px-4 py-2 mb-4 text-sm rounded-lg hover:text-red-400" x-on:click="isUploading = false">
                Cancel
            </button>
        </div>
        <div x-show="videoPath" class="mt-4">
            <video x-bind:src="videoPath" controls class="w-full rounded"></video>
        </div>
        <button 
            x-show='uploadButton'
            wire:click='uploadMovieFile'
            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            Click to Upload
        </button>
    </div>
</div>
