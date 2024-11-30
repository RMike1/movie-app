<?php

use App\Models\Movie;
use Livewire\Livewire;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Livewire\Upload;
use App\Models\User;

it('uploads a movie in chunks', function () {
    Storage::fake('public');
    $chunkSize = 5 * 1024 * 1024; // 5 MB
    $totalChunks = 3;
    $fileName = 'sample_movie.mp4';
    $fileContent = str_repeat('A', $chunkSize * $totalChunks);
    $tempFile = tmpfile();

    fwrite($tempFile, $fileContent);
    $tempFilePath = stream_get_meta_data($tempFile)['uri'];
    $uploadedFile = new UploadedFile($tempFilePath, $fileName, null, null, true);

    for ($chunkNumber = 1; $chunkNumber <= $totalChunks; $chunkNumber++) {
        $start = ($chunkNumber - 1) * $chunkSize;
        $chunkContent = substr($fileContent, $start, $chunkSize);
        $chunkFile = tmpfile();
        fwrite($chunkFile, $chunkContent);
        $chunkPath = stream_get_meta_data($chunkFile)['uri'];
        $chunkUploadedFile = new UploadedFile($chunkPath, $fileName, null, null, true);

        $response = $this->postJson(route('files.upload.large'), [
            'file' => $chunkUploadedFile,
            'resumableChunkNumber' => $chunkNumber,
            'resumableTotalChunks' => $totalChunks,
            'resumableFilename' => $fileName,
        ]);

        dd($response->json());
        $response->assertJsonStructure([
            'path',
            'filename',
            'relativePath',
        ]);
    }
    $assembledFilePath = Storage::disk('public')->path('movies');
    $assembledFile = glob($assembledFilePath . '/*' . $fileName);
    expect(count($assembledFile))->toBe(1);
    fclose($tempFile);
});