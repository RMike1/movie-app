<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Movie;
use Illuminate\Support\Facades\Storage;

class MovieList extends Component
{
    public $movies;
    
    public function deleteMovie(Movie $movie){

        $filePath = $movie->path;
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        } else {
            dd('not found');
        }

        $movie->delete();
    }
    
    #[On('movieUploaded')]
    public function render()
    {
        $movies=$this->movies = Movie::latest('id')->get();
        // dd($movies); 
        return view('livewire.movie-list',compact('movies'));
    }
}