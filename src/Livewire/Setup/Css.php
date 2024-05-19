<?php

namespace Devdojo\Auth\Livewire\Setup;

use Livewire\Component;

class Css extends Component
{
    public $css = '';

    public function mount()
    {
        $css_file = public_path('auth/app.css');
        if (file_exists($css_file)) {
            $this->css = file_get_contents($css_file);
        }

    }

    public function update()
    {
        $css_file = public_path('auth/app.css');
        file_put_contents($css_file, $this->css);
        $this->js('savedMessageOpen()');
    }

    public function render()
    {
        return view('auth::livewire.setup.css');
    }
}
