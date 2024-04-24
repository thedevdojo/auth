<?php

namespace Devdojo\Auth\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SocialProvider extends Model
{
    protected $table = 'social_providers';

    // Indicates if the model should be timestamped.
    public $timestamps = true;

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'slug',
        'scopes',
        'parameters',
        'override_scopes',
        'stateless'
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'scopes' => 'array',
        'parameters' => 'array',
        'override_scopes' => 'boolean',
        'stateless' => 'boolean'
    ];

    // Define a relationship with the SocialProviderUser model
    public function users()
    {
        return $this->hasMany(SocialProviderUser::class, 'social_provider_id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }
}
