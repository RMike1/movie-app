<?php

namespace App\Livewire;

use App\Models\Movie;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
// use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;

class Upload extends Component
{
    use WithFileUploads;

    #[Rule('required','file','mimes:mp4,mov,avi')]
    public $file;
    #[Rule('required','max:255')]
    public $movieName;
    public $chunkSize = 1024 * 1000; 
    public $progress = 0;
    public $error = '';
    public $successMessage = '';
    public $uploadedChunks = 0;
    public $totalChunks;

    public function mount()
    {
        $this->uploadedChunks = 0;
    }

    public function uploadChunk()
    {

        try {
            $fileName = $this->file->getClientOriginalName();
            $fileType = $this->file->extension();
            $filePath = 'movies/temp/' . $fileName;
            $totalFileSize = $this->file->getSize();
            $this->totalChunks = ceil($totalFileSize / $this->chunkSize);

            $stream = fopen($this->file->getRealPath(), 'rb');
            $streamOffset = $this->uploadedChunks * $this->chunkSize;
            fseek($stream, $streamOffset);
            $chunkData = fread($stream, $this->chunkSize);
            Storage::append($filePath, $chunkData);
            fclose($stream);
            $this->uploadedChunks++;
            $this->progress = ($this->uploadedChunks / $this->totalChunks) * 100;
            if ($this->uploadedChunks === $this->totalChunks) {
                $finalPath = 'movies/' . $fileName;
                Storage::move($filePath, $finalPath);

                Movie::create([
                    'movieName' => $this->movieName,
                    'path' => $finalPath,
                    'status' => 'completed',
                ]);

                $this->successMessage = 'File uploaded successfully.';
                $this->progress = 100;
            }
        } catch (\Exception $e) {
            $this->error = 'Failed to upload file. ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
