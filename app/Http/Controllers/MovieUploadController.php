<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Events\FileUploaded;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $tempChunkPath = "{$tempDir}/{$fileName}.part{$chunkIndex}";
        $file->move($tempDir, "{$fileName}.part{$chunkIndex}");

        if ($this->areAllChunksUploaded($fileName, $totalChunks, $tempDir)) {
            $finalPath = $this->assembleChunks($fileName, $totalChunks, $tempDir, $file);
            // event(new FileUploaded(asset('storage/' . $finalPath)));

            return response()->json([
                'path' => asset('storage/' . $finalPath),
                'filename' => $fileName,
                'relativePath'=>$finalPath,
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

        $finalFilePath = 'movies/' . $uniqueFileName;
        $disk = Storage::disk('public');

        $fileHandle = fopen($disk->path($finalFilePath), 'ab');
        for ($i = 1; $i <= $totalChunks; $i++) {
            $chunkPath = "{$tempDir}/{$fileName}.part{$i}";

            if (file_exists($chunkPath)) {
                fwrite($fileHandle, file_get_contents($chunkPath));
                unlink($chunkPath); 
            }
        }
        fclose($fileHandle);

        return $finalFilePath;
    }

    public function streamVideo($filename)
    {
        $path = storage_path('app/public/movies/' . $filename);
        // dd($path);

        if (!file_exists($path)) {
            abort(404);
        }
        $fileSize = filesize($path);
        $mimeType = mime_content_type($path);
        $response = new StreamedResponse(function () use ($path) {
            $handle = fopen($path, 'rb');
            while (!feof($handle)) {
                echo fread($handle, 1024 * 8); 
                ob_flush();
                flush();
            }
            fclose($handle);
        });
        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('Content-Length', $fileSize);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }


    public function show($slug)
    {
        $movie = Movie::where('slug',$slug)->first();
        // dd($movie);
        return view('movies.show', compact('movie'));
    }

}
