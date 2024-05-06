<?php

use App\Models\User;
use Devdojo\Auth\Models\SocialProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Devdojo\Auth\Helper;
use Devdojo\Auth\Traits\HasConfigs;
use function Laravel\Folio\{middleware, name};

middleware(['guest']);
name('auth.register');

new class extends Component
{
    use HasConfigs;

    public $name;
    public $email = '';
    public $password = '';

    public $showNameField = false;
    public $showEmailField = true;
    public $showPasswordField = false;


    public $social_providers = [];

    public function rules()
    {   
        $nameValidationRules = [];
        if(config('devdojo.auth.settings.registration_include_name_field')){
            $nameValidationRules = ['name' => 'required'];
        }

        return [
            ...$nameValidationRules,
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ];
    }

    public function mount(){
        $this->social_providers = Helper::activeProviders();
        $this->loadConfigs();

        if($this->settings->registration_include_name_field){
            $this->showNameField = true;
        }

        if($this->settings->registration_show_password_same_screen){
            $this->showPasswordField = true;
        }
    }

    public function register()
    {
        if(!$this->showPasswordField){
            if($this->settings->registration_include_name_field){
                $this->validateOnly('name');
            }
            $this->validateOnly('email');
            
            $this->showPasswordField = true;
            $this->showNameField = false;
            $this->showEmailField = false;
            $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-password', {})); }, 10);");
            return;
        }

        $this->validate();

        $userData = [
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ];

        if ($this->settings->registration_include_name_field) {
            $userData['name'] = $this->name;
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user, true);

        return redirect()->intended('/');
    }
};

?>

<x-auth::layouts.app title="{{ config('devdojo.auth.language.register.page_title') }}">

    @volt('auth.register')
        <x-auth::elements.container>

            <x-auth::elements.heading 
                :text="($language->register->headline ?? 'No Heading')"
                :description="($language->register->subheadline ?? 'No Description')"
                :show_subheadline="($language->register->show_subheadline ?? false)" />
                
            <form wire:submit="register" class="mt-5 space-y-5">
                
                @if($showNameField)
                    <x-auth::elements.input label="Name" type="text" wire:model="name" autofocus="true" required />
                @endif
                
                @if($showEmailField)
                    @php
                        $autofocusEmail = ($showNameField) ? false : true;
                    @endphp
                    <x-auth::elements.input label="Email Address" type="email" wire:model="email" :autofocus="$autofocusEmail" required />
                @endif
                
                @if($showPasswordField)
                    <x-auth::elements.input label="Password" type="password" wire:model="password" id="password" required />
                @endif
                
                <x-auth::elements.button rounded="md" submit="true">Continue</x-auth::elements.button>
            </form>

            <div class="mt-3 space-x-0.5 text-sm leading-5 text-left" style="color:{{ config('devdojo.auth.appearance.color.text') }}">
                <span class="opacity-[47%]">Already have an account?</span>
                <x-auth::elements.text-link href="{{ route('auth.login') }}">Sign in</x-auth::elements.text-link>
            </div>

            @if(count($this->social_providers))
                <x-auth::elements.separator class="my-7">or</x-auto::elements.separator>
                <div class="relative space-y-2 w-full">
                    @foreach($this->social_providers as $slug => $provider)
                        <x-auth::elements.social-button :$slug :$provider />    
                    @endforeach
                </div>
            @endif


        </x-auth::elements.container>
    @endvolt

</x-auth::layouts.app>