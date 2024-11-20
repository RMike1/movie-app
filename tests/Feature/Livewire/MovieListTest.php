<?php

use App\Livewire\MovieList;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(MovieList::class)
        ->assertStatus(200);
});
