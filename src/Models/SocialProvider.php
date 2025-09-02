<?php

namespace Devdojo\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * Class User
 *
 * @property string|null $client_id
 * @property string|null $client_secret
 */
class SocialProvider extends Model
{
    use Sushi;

    protected $rows = [];

    public function getRows()
    {
        // Fetching the social providers from the configuration file
        $rowsArray = [];
        $socialProviders = config('devdojo.auth.providers', []);

        foreach ($socialProviders as $key => $provider) {
            $provider['slug'] = $key;

            if (isset($provider['scopes']) && is_array($provider['scopes'])) {
                $provider['scopes'] = implode(',', $provider['scopes']);
            }

            array_push($rowsArray, $provider);
        }

        $this->rows = $rowsArray;

        return $this->rows;
    }

    protected function sushiShouldCache()
    {
        if (app()->isLocal()) {
            return false;
        }

        return true;
    }
}
