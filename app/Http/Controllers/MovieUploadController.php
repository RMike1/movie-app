<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Events\FileUploaded;
use Illuminate\Support\Facades\Response;
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
        $filePath = Storage::disk('public')->path('movies/' . $filename);
        if (!Storage::disk('public')->exists('movies/' . $filename)) {
            abort(404, 'File not found.');
        }
        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath);
        $range = request()->header('Range');
        $start = 0;
        $end = $fileSize - 1;
        if ($range) {
            preg_match('/bytes=(\d+)-(\d*)/', $range, $matches);
            $start = (int)$matches[1];
            $end = $matches[2] ? (int)$matches[2] : $fileSize - 1;
        }
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => ($end - $start) + 1,
            'Content-Range' => "bytes $start-$end/$fileSize",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache',
        ];
        $file = fopen($filePath, 'rb');
        fseek($file, $start);
        return new StreamedResponse(function () use ($file, $end) {
            $buffer = 1024 * 8; // 8KB buffer size
            while (ftell($file) <= $end && !feof($file)) {
                echo fread($file, $buffer);
                flush();
            }
            fclose($file);
        }, 206, $headers);
    }
    public function show($slug)
    {
        $movie = Movie::where('slug',$slug)->first();
        // dd($movie);
        return view('movies.show', compact('movie'));
    }

}
