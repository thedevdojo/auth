<?php

use App\Models\User;
use function Laravel\Folio\{middleware, name};
use Illuminate\Support\Facades\Route;

middleware(['guest']);
name('auth.two-factor-challenge');

new class extends Component
{
    public $recovery = false;

    public function mount()
    {
        $this->recovery = false;
    }

    public function switchToRecovery()
    {
        $this->recovery = !$this->recovery;
    }

    public function authenticate()
    {
        // Authentication logic here
    }
}

?>

<x-auth::layouts.app title="{{ config('devdojo.auth.language.register.page_title') }}">
    @volt('auth.two-factor-challenge')
        <x-auth::elements.container>
        
            <x-auth::elements.heading 
                :text="($language->twoFactorChallenge->headline ?? 'No Heading')"
                :description="($language->twoFactorChallenge->subheadline ?? 'No Description')"
                :show_subheadline="($language->twoFactorChallenge->show_subheadline ?? false)" />

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                @if(!$recovery)
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Code') }}</label>

                        <div class="col-md-6">
                            <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" required autofocus>

                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                @else
                    <div class="form-group row">
                        <label for="recovery_code" class="col-md-4 col-form-label text-md-right">{{ __('Recovery Code') }}</label>

                        <div class="col-md-6">
                            <input id="recovery_code" type="text" class="form-control @error('recovery_code') is-invalid @enderror" name="recovery_code" required autofocus>

                            @error('recovery_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="mb-0 form-group row">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>

                        <a href="#" wire:click="switchToRecovery" class="btn btn-link">
                            @if(!$recovery)
                                {{ __('Use a recovery code') }}
                            @else
                                {{ __('Use an authentication code') }}
                            @endif
                        </a>
                    </div>
                </div>
            </form>
        </x-auth::elements.container>
    @endvolt
</x-auth::layouts.app>
