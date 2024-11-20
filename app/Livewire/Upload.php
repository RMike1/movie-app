<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use App\Models\Movie;
use Livewire\WithFileUploads;

class upload extends Component
{
    use WithFileUploads;
    public $file;
    public $movieName;
    public $progress = 0;
    public $error = '';
    public $successMessage = '';

    public function uploadChunk()
    {
        $this->validate([
            'file' => 'required|file|mimes:mp4,mov,avi|max:102400',
            'movieName' => 'required|string|max:255',
        ]);
    
        try {
            $fileName = $this->file->getClientOriginalName();
            $filePath = 'movies/' . $fileName;
    
            Storage::disk('public')->put($filePath, file_get_contents($this->file->getRealPath()));
    
            Movie::create([
                'movieName' => $this->movieName,
                'path' => $filePath,
                'status' => 'completed',
            ]);
    
            $this->progress = 100;
            $this->successMessage = 'File uploaded successfully.';
        } catch (\Exception $e) {
            $this->error = 'Failed to upload file. ' . $e->getMessage();
        }
    }
    
    public function render()
    {
        return view('livewire.upload');
    }
}