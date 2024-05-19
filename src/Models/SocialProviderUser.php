<?php

namespace Devdojo\Auth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SocialProviderUser extends Model
{
    protected $table = 'social_provider_user';

    // Indicates if the model should be timestamped.
    public $timestamps = true;

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id',
        'provider_slug',
        'provider_user_id',
        'nickname',
        'name',
        'email',
        'avatar',
        'provider_data',
        'token',
        'refresh_token',
        'token_expires_at',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'token_expires_at' => 'datetime',
        'provider_data' => 'array',
    ];

    // Define a relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define a relationship to the SocialProvider model via Sushi
    public function socialProvider()
    {
        return $this->belongsTo(SocialProvider::class, 'provider_slug', 'slug');
    }
}
