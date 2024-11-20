<?php

use App\Livewire\Upload;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Upload::class)
        ->assertStatus(200);
});
