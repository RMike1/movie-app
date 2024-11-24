<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieUploadController extends Controller
{
    public function uploadLargeFiles(Request $request)
    {
        $file = $request->file('file'); // The current chunk
        $chunkIndex = $request->input('resumableChunkNumber'); // The chunk index (1-based)
        $totalChunks = $request->input('resumableTotalChunks'); // Total number of chunks
        $fileName = $request->input('resumableFilename'); // Original file name
        $tempDir = storage_path('app/public/chunks'); // Temporary directory for chunks
        // Create temporary directory if it doesn't exist
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        // Save the current chunk to the temp directory
        $tempChunkPath = $tempDir . "/{$fileName}.part{$chunkIndex}";
        $file->move($tempDir, "{$fileName}.part{$chunkIndex}");

        // Check if all chunks are uploaded
        if ($this->areAllChunksUploaded($fileName, $totalChunks, $tempDir)) {
            // Once all chunks are uploaded, assemble them into the final video file
            $finalPath = $this->assembleChunks($fileName, $totalChunks, $tempDir,$file);
            // Return the URL for the video preview
            // dd($finalPath);
            return [
                'path' => asset('storage/' . $finalPath),
                'filename' => $fileName,
            ];
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
    private function assembleChunks($fileName, $totalChunks, $tempDir,$file)
    {
        $extension = $file->getClientOriginalExtension();
        $fileNamepath = str_replace('.'.$extension, '', $file->getClientOriginalName()); 
        $fileNamepath .= '_' . md5(time()) . '.' . $extension; 
        

        $finalFilePath = 'videos/' . $fileNamepath; // The path where the final file will be stored
        $disk = Storage::disk('public');
        // Initialize an empty file in the final directory
        $fileHandle = $disk->put($fileNamepath, '');
        // $finalFilePath = $disk->put('videos', $fileName);

        // Append each chunk to the final file
        for ($i = 1; $i <= $totalChunks; $i++) {
            $chunkPath = "{$tempDir}/{$fileName}.part{$i}";
            $disk->append($finalFilePath, file_get_contents($chunkPath));
            unlink($chunkPath); // Delete the chunk after appending
        }

        return $finalFilePath;
    }
}