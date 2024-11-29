<div class="max-w-3xl mx-auto mt-4 p-6 bg-gray-900 shadow-md rounded-lg">
    <h2 class="text-2xl text-white text-center font-bold mb-4">Upload Movie</h2>
    <div 
        x-data="{
            isUploading: @entangle('isUploading'),
            progress: @entangle('progress'),
            videoPath: @entangle('videoPath'),
            uploadButton: @entangle('uploadButton'),
            isPaused: @entangle('isPaused'),
            resumable: null,
            successMessageAlert:@entangle('successMessageAlert'),

            init() {
                const self = this;
                this.resumable = new Resumable({
                    target: '{{ route('files.upload.large') }}',
                    query: { _token: '{{ csrf_token() }}' },
                    fileType: ['mp4', 'mkv', 'avi', 'mov', 'webm', 'mpg', 'mpeg'],
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
                    self.videoPath = false;
                    self.uploadButton = false;
                });

                this.resumable.on('fileSuccess', function(file, response) {
                    const data = JSON.parse(response);
                    self.videoPath = data.path;
                    self.isUploading = false;
                    self.uploadButton = true;
                    @this.set('fileImageData', data.relativePath);
                });

                this.resumable.on('fileError', function() {
                    alert('File uploading error.');
                    self.isUploading = false;
                });
            },

            pauseUpload() {
                this.isPaused = true;
                this.resumable.pause();
            },

            resumeUpload() {
                this.isPaused = false;
                this.resumable.upload();
            },

            cancelUpload() {
                this.isUploading = false;
                this.resumable.cancel();
                @this.cancelUpload('{{$movieName}}'); 
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
                <input type="file"  class="hidden"  x-ref="fileInput" />
                <input type="text" class="hidden" wire:model="fileImageData" x-bind:value="videoPath"/> 
                


                @error('fileImageData')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            
        </div>

        <div x-show="isUploading" x-cloak class="w-full bg-gray-200 rounded-lg h-6">
            <div class="bg-gray-500 h-full rounded-lg text-xs text-white text-center" x-cloak :style="{ width: `${progress}%` }"
                x-text="`${progress}%`">
            </div>
            <div class="text-md text-white mt-2">
                Uploading..
            </div>
        </div>

        <div class="flex justify-between mt-4">
            <button 
                x-show="isUploading && !isPaused"
                @click="pauseUpload"
                x-cloak
                class="text-white px-4 py-2 mt-2 rounded-lg outline outline-offset-2 outline-red-300/50 hover:bg-red-300/50">
                Pause
            </button>

            <button 
                x-show="isUploading && isPaused"
                x-cloak
                @click="resumeUpload"
                class="text-white px-4 py-2 mt-2 rounded-lg outline outline-offset-2 outline-green-600/50 hover:bg-green-600/50">
                Resume
            </button>
            <button 
                x-show="isUploading"
                x-cloak
                x-on:click="cancelUpload()"
                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                Cancel
            </button>
        </div>

        <div x-show="videoPath" class="mt-4">
            <video x-bind:src="videoPath" controls class="w-full rounded"></video>
        </div>

        <button 
            x-show="uploadButton"
            x-cloak
            wire:click="uploadMovieFile"
            type="submit"
            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <span wire:loading.remove wire:target='uploadMovieFile'>Click to Save</span>
            <span wire:loading wire:target="uploadMovieFile">Saving...</span>
        </button>
            
        <button 
                x-show="uploadButton"
                x-cloak
                x-on:click="cancelUpload()"
                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                Cancel
        </button>

        <div 
            class="text-xl text-white pt-2 px-4 py-2" 
            x-data x-cloak x-show="successMessageAlert" x-transition:enter="transition ease-out duration-300" x-effect="if (successMessageAlert) {setTimeout(() => successMessageAlert = false, 5000);}">
            <span>Saved..</span>
        </div>


    </div>
</div>
