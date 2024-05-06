<?php

namespace Devdojo\Auth\Livewire\Setup;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Winter\LaravelConfigWriter\ArrayFile;

class Alignment extends Component
{
    public $heading;
    public $container;

    public function mount(){
        $this->heading = config('devdojo.auth.appearance.alignment.heading');
        $this->container = config('devdojo.auth.appearance.alignment.container');    

        if(!isset($this->heading)){
            $this->heading = 'center';
        }

        if(!isset($this->container)){
            $this->container = 'center';
        }
    }

    public function updatingHeading($value){
        $this->updateConfigKeyValue('alignment.heading', $value);
    }

    public function updatingContainer($value){
        $this->updateConfigKeyValue('alignment.container', $value);
    }

    private function updateConfigKeyValue($key, $value){
        $config = ArrayFile::open(base_path('config/devdojo/auth/appearance.php'));
        $config->set($key, $value);
        $config->write();

        Artisan::call('config:clear');

        $this->js('savedMessageOpen()');
    }

    public function render()
    {
        return view('auth::livewire.setup.alignment');
    }
}