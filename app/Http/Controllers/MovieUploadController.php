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
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $tempChunkPath = $tempDir . "/{$fileName}.part{$chunkIndex}";
        $file->move($tempDir, "{$fileName}.part{$chunkIndex}");
        if ($this->areAllChunksUploaded($fileName, $totalChunks, $tempDir)) {
            $finalPath = $this->assembleChunks($fileName, $totalChunks, $tempDir,$file);
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
        $fileHandle = $disk->put($fileNamepath, '');
        for ($i = 1; $i <= $totalChunks; $i++) {
            $chunkPath = "{$tempDir}/{$fileName}.part{$i}";
            $disk->append($finalFilePath, file_get_contents($chunkPath));
            unlink($chunkPath); 
        }

        return $finalFilePath;
    }
}