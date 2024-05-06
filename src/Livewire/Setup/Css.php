<?php

namespace Devdojo\Auth\Livewire\Setup;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Winter\LaravelConfigWriter\ArrayFile;

class Css extends Component
{
    public function mount(){
        
    }

    public function render()
    {
        return view('auth::livewire.setup.css');
    }
}