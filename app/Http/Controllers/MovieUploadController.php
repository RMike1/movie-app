<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieUploadController extends Controller
{
    public function uploadLargeFiles(Request $request)
    {
        $file = $request->file('file');
        $chunkIndex = $request->input('resumableChunkNumber');
        $totalChunks = $request->input('resumableTotalChunks');
        $fileName = $request->input('resumableFilename');
        $tempDir = storage_path('app/public/chunks');

        // Ensure temporary directory exists
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Save the uploaded chunk
        $tempChunkPath = "{$tempDir}/{$fileName}.part{$chunkIndex}";
        $file->move($tempDir, "{$fileName}.part{$chunkIndex}");

        // Check if all chunks have been uploaded
        if ($this->areAllChunksUploaded($fileName, $totalChunks, $tempDir)) {
            $finalPath = $this->assembleChunks($fileName, $totalChunks, $tempDir, $file);
            return response()->json([
                'path' => asset('storage/' . $finalPath),
                'filename' => $fileName,
            ]);
        }

        return response()->json(['status' => 'chunk uploaded']);
    }

    private function areAllChunksUploaded($fileName, $totalChunks, $tempDir)
    {
        for ($i = 1; $i <= $totalChunks; $i++) {
            if (!file_exists("{$tempDir}/{$fileName}.part{$i}")) {
                return false;
            }
        }
        return true;
    }

    private function assembleChunks($fileName, $totalChunks, $tempDir, $file)
    {
        $extension = $file->getClientOriginalExtension();
        $fileNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $uniqueFileName = $fileNameWithoutExtension . '_' . md5(time()) . '.' . $extension;

        $finalFilePath = 'videos/' . $uniqueFileName;
        $disk = Storage::disk('public');

        // Open final file stream for appending chunks
        $fileHandle = fopen($disk->path($finalFilePath), 'ab');
        for ($i = 1; $i <= $totalChunks; $i++) {
            $chunkPath = "{$tempDir}/{$fileName}.part{$i}";

            if (file_exists($chunkPath)) {
                // Append chunk to final file
                fwrite($fileHandle, file_get_contents($chunkPath));
                unlink($chunkPath); // Delete chunk after appending
            }
        }
        fclose($fileHandle);

        return $finalFilePath;
    }

    public function uploadMovie(Request $request)
    {
        // $movieName = $request->movieName;
        // $videoPath = $request->fileImage; 
        $validated=$request->validate([
            'fileImage' => 'required|file|mimes:mp4,mov,avi,mpg,mpeg,mkv,avi',
            'movieName' => 'required|max:255',
        ]);

        $filePath = $request->fileImage->store('movies', 'public');
        Movie::create([
            'movieName' => $validated['movieName'],
            'path' => $filePath,
            'status' => 'completed',
        ]);
        return redirect()->route('dashboard')->with('success', 'Movie uploaded successfully!');
    }
}
