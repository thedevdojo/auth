<?php

namespace Devdojo\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

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

    public function getSchema()
    {
        return [
            'id' => $this->autoIncrement(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'scopes' => $this->string()->nullable(),
            'parameters' => $this->string()->nullable(),
            'stateless' => $this->boolean(),
            'active' => $this->boolean(),
            'svg' => $this->text(),
        ];
    }
}
