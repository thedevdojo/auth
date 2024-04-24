<?php

namespace Devdojo\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialProviderUser extends Model
{
    protected $table = 'social_provider_user';

    // Indicates if the model should be timestamped.
    public $timestamps = true;

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id',
        'social_provider_id',
        'token',
        'refresh_token',
        'token_expires_at',
        'provider_specific_data'
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'token_expires_at' => 'datetime',
        'provider_specific_data' => 'array'
    ];

    // Define a relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define a relationship to the SocialProvider model
    public function socialProvider()
    {
        return $this->belongsTo(SocialProvider::class, 'social_provider_id');
    }
}
