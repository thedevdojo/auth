<?php

use App\Models\User;
use Illuminate\Auth\Events\Login;
use function Laravel\Folio\{middleware, name};
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Devdojo\Auth\Traits\HasConfigs;

if(!isset($_GET['preview']) || (isset($_GET['preview']) && $_GET['preview'] != true) || !app()->isLocal()){
    middleware(['guest']);
}

name('auth.login');

new class extends Component
{
    use HasConfigs;
    
    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $password = '';

    public $showPasswordField = false;

    public $appearance = [];
    public $language = [];

    public function mount(){
        $this->loadConfigs();
    }

    public function editIdentity(){
        $this->showPasswordField = false;
    }

    public function authenticate()
    {
        if(!$this->showPasswordField){
            $this->validateOnly('email');
            $this->showPasswordField = true;
            $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-password', {})); }, 10);");
            return;
        }
        
        
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->addError('password', trans('auth.failed'));
            return;
        }

        dd('waht');

        event(new Login(auth()->guard('web'), User::where('email', $this->email)->first(), true));

        return redirect()->intended('/');
    }
};

?>

<x-auth::layouts.app title="{{ config('devdojo.auth.language.login.page_title') }}">
    @volt('auth.login') 
        <x-auth::elements.container>
        
                <x-auth::elements.heading 
                    :text="($language->login->headline ?? 'No Heading')" 
                    :align="($appearance->heading_align ?? 'center')" 
                    :description="($language->login->subheadline ?? 'No Description')"
                    :show_subheadline="($language->login->show_subheadline ?? false)" />
                
                <form wire:submit="authenticate" class="mt-5 space-y-5">

                    @if($showPasswordField)
                        <x-auth::elements.input-placeholder value="{{ $email }}">
                            <button type="button" wire:click="editIdentity" class="font-medium text-blue-500">Edit</button>
                        </x-auth::elements.input-placeholder>
                    @else  
                        <x-auth::elements.input label="Email Address" type="email" wire:model="email" autofocus="true" id="email" required />
                    @endif
                    
                    @if($showPasswordField)
                        <x-auth::elements.input label="Password" type="password" wire:model="password" id="password" />
                        <div class="flex justify-between items-center mt-6 text-sm leading-5">
                            <x-auth::elements.text-link href="{{ route('auth.password.request') }}">Forgot your password?</x-auth::elements.text-link>
                        </div>
                    @endif

                    <x-auth::elements.button type="primary" rounded="md" size="md" submit="true">Continue</x-auth::elements.button>
                </form>
                
                
                <div class="mt-3 space-x-0.5 text-sm leading-5 text-left text-gray-400 dark:text-gray-300">
                    <span>Don't have an account?</span>
                    <x-auth::elements.text-link href="{{ route('auth.register') }}">Sign up</x-auth::elements.text-link>
                </div>

        </x-auth::elements.container>
    @endvolt
</x-auth::layouts.app>