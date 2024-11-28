<?php

namespace App\Livewire;

use App\Models\Movie;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;



class Upload extends Component
{
    use WithFileUploads;

    #[Rule('required|max:255')]
    public $movieName;
    public $fileImage=[]; 
    public $videoPath;
    public $isUploading = false;
    public $isPaused = false;
    public $uploadButton = false;
    public $progress = 0;

    public function uploadMovieFile()
    {

        foreach($this->fileImage as $imageData){
            $filePath = $imageData->store('movies', 'public');
        }
        Movie::create([
            'movieName' => $this->movieName,
            'path' => $filePath,
        ]);
        $this->reset(['fileImage', 'movieName']);
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
