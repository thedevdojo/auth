<?php

use App\Models\User;
use Illuminate\Auth\Events\Login;
use function Laravel\Folio\{middleware, name};
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Devdojo\Auth\Traits\HasConfigs;

/*$vendor_folder_location = base_path('vendors/devdojo/auth/resources/views/includes')  . '/volt-page-dynamic-middleware-name.php';
$package_folder_location = base_path('packages/devdojo/auth/resources/views/includes') . '/volt-page-dynamic-middleware-name.php';
if(file_exists($package_folder_location)){
    include($package_folder_location);
}

if(file_exists($vendor_folder_location)){
    include($vendor_folder_location);
}*/

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

    public $language = [];

    public $twoFactorEnabled = true;

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

        $credentials = ['email' => $this->email, 'password' => $this->password];
        

        if(!\Auth::validate($credentials)){
            $this->addError('password', trans('auth.failed'));
            return;
        }
        
        $userAttemptingLogin = User::where('email', $this->email)->first();

        if(!isset($userAttemptingLogin->id)){
            $this->addError('password', trans('auth.failed'));
            return;
        }

        if($this->twoFactorEnabled && !is_null($userAttemptingLogin->two_factor_confirmed_at)){
            // We want this user to login via 2fa
            session()->put([
                'login.id' => $userAttemptingLogin->getKey()
            ]);

            return redirect()->route('auth.two-factor-challenge');

        } else {
            if (!Auth::attempt($credentials)) {
                $this->addError('password', trans('auth.failed'));
                return;
            }
            event(new Login(auth()->guard('web'), User::where('email', $this->email)->first(), true));

            return redirect()->intended('/');
        }

        

        /*$request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->boolean('remember'),
        ]);*/

        
    }
};

?>

<x-auth::layouts.app title="{{ config('devdojo.auth.language.login.page_title') }}">
    @volt('auth.login') 
        <x-auth::elements.container>
        
                <x-auth::elements.heading 
                    :text="($language->login->headline ?? 'No Heading')"
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
                
                
                <div class="mt-3 space-x-0.5 text-sm leading-5 text-left" style="color:{{ config('devdojo.auth.appearance.color.text') }}">
                    <span class="opacity-[47%]">Don't have an account?</span>
                    <x-auth::elements.text-link href="{{ route('auth.register') }}">Sign up</x-auth::elements.text-link>
                </div>

        </x-auth::elements.container>
    @endvolt
</x-auth::layouts.app>