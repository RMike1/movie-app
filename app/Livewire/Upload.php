<?php

namespace App\Livewire;

use App\Models\Movie;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    // #[Rule('required|max:255')]
    public $movieName;
    public $fileImageData=''; 
    public $videoPath;
    public $isUploading = false;
    public $isPaused = false;
    public $uploadButton = false;
    public $progress = 0;
    public $successMessageAlert=false;


    public function uploadMovieFile()
    {
        $validatedData = $this->validate([
            'movieName' => 'required|max:255|unique:movies',
        ]);

        // dd($this->fileImageData);
        Movie::create([
            'movieName' => $validatedData['movieName'],
            'path' => $this->fileImageData,
            'slug' => Str::slug($validatedData['movieName']),
        ]);

        $this->reset(['fileImageData', 'movieName']);
        $this->progress = 0;
        $this->uploadButton = false;
        $this->successMessageAlert = true;
        $this->dispatch('movieUploaded', ['message' => 'Movie uploaded successfully!']);
    }

    public function cancelUpload($fileName)
    {
        $tempDir = storage_path('app/public/chunks');
        $files = glob("{$tempDir}/{$fileName}*.part*");

        foreach ($files as $file) {
            unlink($file);
        }

        $this->isUploading = false;
        $this->resetUpload();
    }

    public function resetUpload()
    {
        $this->progress = 0;
        $this->uploadButton = false;
        $this->videoPath = null;
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
