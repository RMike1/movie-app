<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieUploadController extends Controller
{
    public function uploadLargeFiles(Request $request)
    {
        $file = $request->file('file');
        $chunkNumber = $request->input('resumableChunkNumber');
        $totalChunks = $request->input('resumableTotalChunks');
        $fileName = $request->input('resumableFilename');
        $tempDir = storage_path('app/public/chunks');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempChunkPath = $tempDir .'/'. $fileName . '.part' . $chunkNumber;
        $file->move(dirname($tempChunkPath), basename($tempChunkPath));

        if ($chunkNumber == $totalChunks) {
            $finalPath = $this->assembleChunks($fileName, $totalChunks, $tempDir, $file);
         }
            return response()->json([
                'path' => asset('storage/' . $finalPath),
                'filename' => $fileName,
                'relativePath'=>$finalPath,
            ]);
    }

    private function assembleChunks($fileName, $totalChunks, $tempDir, $file)
    {
        $uniqueFileName = uniqid('movie_', true) . '.' . $file->getClientOriginalExtension();
        $finalFilePath = 'movies/' . $uniqueFileName;
        $disk = Storage::disk('public');

        $fileHandle = fopen($disk->path($finalFilePath), 'ab');
        for ($i = 1; $i <= $totalChunks; $i++) {
            $chunkPath = $tempDir .'/'. $fileName . '.part' . $i;

            if (file_exists($chunkPath)) {
                fwrite($fileHandle, file_get_contents($chunkPath));
                unlink($chunkPath); 
            }
        }
        fclose($fileHandle);
        return $finalFilePath;
    }
    public function show($slug)
    {
        $movie = Movie::where('slug',$slug)->first();
        return view('movies.show', compact('movie'));
    }

}
