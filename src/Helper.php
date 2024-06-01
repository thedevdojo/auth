<?php

namespace Devdojo\Auth;

class Helper
{
    // Build your next great package.
    public static function activeProviders()
    {
        $providers = config('devdojo.auth.providers');
        $activeProviders = [];
        foreach ($providers as $slug => $provider) {
            if ($provider['active']) {
                $activeProviders[$slug] = (object) $provider;
            }
        }

        return $activeProviders;
    }

    public static function getProvidersFromArray($array)
    {
        $providers = config('devdojo.auth.providers');
        $providersInArray = [];
        foreach ($providers as $slug => $provider) {
            if ($provider['active'] && in_array($slug, $array)) {
                $providersInArray[$slug] = (object) $provider;
            }
        }

        return $providersInArray;
    }

    public static function convertSlugToTitle($slug)
    {
        $readable = str_replace('_', ' ', str_replace('-', ' ', $slug));

        return ucwords($readable);
    }

    public static function convertHexToRGBString($hex)
    {
        // Remove the '#' character if present
        $hex = str_replace('#', '', $hex);

        // Ensure the hex string is properly formatted
        if (strlen($hex) === 3) {
            $hex = str_repeat($hex[0], 2).str_repeat($hex[1], 2).str_repeat($hex[2], 2);
        } elseif (strlen($hex) !== 6) {
            throw new \Exception('Invalid hex color length');
        }

        // Split the hex color into its RGB components
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Return the RGB string
        return "$r $g $b";
    }
}
