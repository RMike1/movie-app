<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Movie;

class MovieList extends Component
{
    public $movies;
    
    public function deleteMovie(Movie $movie){
        $movie->delete();
    }
    
    #[On('movieUploaded')]
    public function render()
    {
        $movies=$this->movies = Movie::all();
        return view('livewire.movie-list',compact('movies'));
    }
}

