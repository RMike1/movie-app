<?php

use App\Models\User;
use App\Models\Movie;
use Livewire\Livewire;
use App\Livewire\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

it('upload movie file in chunks', function () {
    Storage::fake('public'); 

    Storage::disk('public')->makeDirectory('chunks');
    Storage::disk('public')->makeDirectory('movies');

    $file = UploadedFile::fake()->create('movie.mp4', 500); 
    $totalChunks = 3; 
    $fileName = 'movie.mp4'; 
    for ($i = 1; $i <= $totalChunks; $i++) {
        $response = $this->postJson(route('files.upload.large'), [
            'file' => $file,
            'resumableChunkNumber' => $i,
            'resumableTotalChunks' => $totalChunks,
            'resumableFilename' => $fileName,
        ]);
        $response->assertStatus(200);
    }
    $response->assertJsonStructure(['path', 'filename', 'relativePath']);
    $finalFilePath = 'movies/movie.mp4';
    Storage::disk('public')->assertExists($finalFilePath);
});
