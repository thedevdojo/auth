<?php

namespace Devdojo\Auth;

class Helper
{
    // Build your next great package.
    public static function activeProviders(){
        $providers = config('devdojo.auth.providers');
        $activeProviders = [];
        foreach($providers as $provider){
            if($provider['active']){
                $activeProviders[] = (object)$provider;
            }
        }
        return $activeProviders;
    }

    public static function convertSlugToTitle($slug){
        $readable = str_replace('_', ' ', str_replace('-', ' ', $slug)); 
        return ucwords($readable); 
    }
}
