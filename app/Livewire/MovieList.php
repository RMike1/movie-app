<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Movie;

class MovieList extends Component
{
    public $movies = [];

    public function mount()
    {
        $this->movies = Movie::where('status', 'completed')->get();
    }

    public function render()
    {
        return view('livewire.movie-list');
    }
}

