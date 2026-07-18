<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Middleware;
use Livewire\Attributes\Validate;
use Devdojo\Auth\Traits\HasConfigs;

if(!isset($_GET['preview']) || (!$_GET['preview']) || !app()->isLocal()){
    middleware('auth');
}

new #[Layout('auth::layouts.app')]
#[Middleware('auth')]
class extends Component
{
    use HasConfigs;

    #[Validate('required|current_password')]
    public $password = '';

    public function mount(){
        $this->loadConfigs();
    }

    public function confirm()
    {
        $this->validate();

        session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(config('devdojo.auth.settings.redirect_after_auth'));
    }
};

?>

<x-auth::elements.container>
            <x-auth::elements.heading
                :text="($language->passwordConfirm->headline ?? 'No Heading')"
                :description="($language->passwordConfirm->subheadline ?? 'No Description')"
                :show_subheadline="($language->passwordConfirm->show_subheadline ?? false)"
            />
            <form wire:submit="confirm" class="space-y-5">
                <x-auth::elements.input :label="config('devdojo.auth.language.passwordConfirm.password')" type="password" id="password" name="password" data-auth="password-input" autofocus="true" wire:model="password" autocomplete="current-password" />
                <x-auth::elements.button type="primary" rounded="md" data-auth="submit-button" submit="true">{{config('devdojo.auth.language.passwordConfirm.button')}}</x-auth::elements.button>
            </form>
        </x-auth::elements.container>
    