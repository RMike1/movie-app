<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\FileUploaded;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MovieStreamController extends Controller
{
    public function __invoke($filename)
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
}
