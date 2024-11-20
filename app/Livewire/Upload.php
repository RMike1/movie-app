<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use App\Models\Movie;

class upload extends Component
{
    public $file;
    public $progress = 0;
    public $error = '';
    public $successMessage = '';

    public function uploadChunk()
    {
        if (!$this->file) {
            $this->error = 'Please select a file.';
            return;
        }

        try {
            $fileName = $this->file->getClientOriginalName();
            $filePath = 'movies/' . $fileName;

            // Save the file (chunked upload logic can go here)
            Storage::disk('public')->put($filePath, file_get_contents($this->file->getRealPath()));

            // Save to the database
            Movie::create([
                'name' => $fileName,
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

