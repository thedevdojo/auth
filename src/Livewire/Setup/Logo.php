<?php

namespace Devdojo\Auth\Livewire\Setup;

use Livewire\Component;
use Winter\LaravelConfigWriter\ArrayFile;
use Livewire\WithFileUploads;

class Logo extends Component
{
    use WithFileUploads;

    public $data;

    public $logo_type;
    public $logo_image_src;
    public $logo_svg_string;
    public $logo_height;

    public $image_uploaded = false;
    public $logo_image;

    public $title;
    public $description;

    public function mount(){
        $this->data = config('foundation.base');
        

        $this->logo_type = config('devdojo.auth.appearance.logo.type');
        $this->logo_image_src = config('devdojo.auth.appearance.logo.image_src');
        $this->logo_svg_string = config('devdojo.auth.appearance.logo.svg_string');
        $this->logo_height = config('devdojo.auth.appearance.logo.height');

        if($this->logo_image_src){
            $this->logo_image = true;
        }
        

        //$this->setLogoValue();


    }

    public function logoImageChange($file){
        dd('hit');
    }

    public function upload()
    {
        $this->photo->store(path: 'photos');
    }

    protected function rules(){
        
        return [
            'logo_type' => 'required',
            'logo_image_src' => 'required',
            'logo_image_string' => 'required',
            'logo_height' => 'required'
        ];
    }


    public function updateSvg($value){
        $this->updateConfigKeyValue('logo.svg_string', $value);
    }

    public function updating($property, $value)
    {
        if($property == 'logo_image'){
            // dd('up');
            $filename = $value->getFileName();
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            $value->storeAs('public/auth', 'logo.' . $extension);
            $this->logo_image_src = '/storage/auth/logo.png';

            $this->updateConfigKeyValue('logo.image_src', '/storage/auth/logo.png');

            $value = null;
            // dd($this->logo_src);
            //dd($this->logo_image);
        }

        if($property == 'logo_type'){
            $this->updateConfigKeyValue('logo.type', $value);
        }

        if($property == 'logo_height'){
            $this->updateConfigKeyValue('logo.height', $value);
        }
    }

    private function updateConfigKeyValue($key, $value){
        $config = ArrayFile::open(base_path('config/devdojo/auth/appearance.php'));
        $config->set($key, $value);
        $config->write();
        $this->js('savedMessageOpen()');
    }

    protected function saveData($array)
    {
        $config = ArrayFile::open(base_path('config/foundation/base.php'));

        foreach($array as $key => $value){
            $config->set($key, $value);
        }

        $config->write();
    }

    protected $validationAttributes = [
        'data.logo' => 'logo'
    ];

    

    public function submit(){
        
        
        $this->data['logo'] = $this->logoValue();
        
        $validatedData = $this->validate();


        $this->saveData([
            'logo' => $this->data['logo'],
            'logo_type' => $this->logo_type
        ]);

        $this->emitUp('nextStep');
    }

    public function logoValue(){
        
        $logo = match ($this->logo_type) {
            'image' => $this->logo_image,
            'svg' => $this->logo_svg,
            'text' => $this->logo_text
        };

        return $logo;
    }

    public function setLogoValue(){
        if(isset($this->data['logo'])){
            if($this->logo_type == 'image'){
                $this->logo_image = $this->data['logo'];
            } elseif($this->logo_type == 'svg'){
                $this->logo_svg = $this->data['logo'];
            } elseif($this->logo_type == 'text'){
                $this->logo_text = $this->data['logo'];
            }
        }
    }
    public function render()
    {
        return view('auth::livewire.setup.logo');
    }
}
