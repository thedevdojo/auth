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
        $this->rows = config('devdojo.auth.providers', []);

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
