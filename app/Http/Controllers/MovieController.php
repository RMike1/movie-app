<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function edit(Movie $movie)
    {
        return view('movies.edit', compact('movie')); 
    }
    public function destroy(Movie $movie)
    {
        if (Storage::exists($movie->path)) {
            Storage::delete($movie->path);
        }
        $movie->delete();

        return redirect()->route('dashboard')->with('success', 'Movie deleted successfully.');
    }
}

