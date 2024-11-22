<?php

namespace App\Livewire;

use App\Models\Movie;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    public $fileImage;
    public $movieName;

    public function uploadMovieFile()
    {

        dd($this->fileImage);

        $this->validate([
            'file' => 'required|file|mimes:mp4,mov,avi',
            'movieName' => 'required|max:255',
        ]);
        $filePath = $this->fileImage->store('movies', 'public');
        

        Movie::create([
            'movieName' => $this->movieName,
            'path' => $filePath,
            'status'=>'completed'
        ]);

        $this->reset(['file', 'movieName']);
        $this->dispatchBrowserEvent('movieUploaded', ['message' => 'Movie uploaded successfully!']);
    }

    public function render()
    {
        return view('livewire.upload');
    }
}
