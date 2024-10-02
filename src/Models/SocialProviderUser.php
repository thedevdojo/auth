<?php

namespace Devdojo\Auth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialProviderUser extends Model
{
    protected $table = 'social_provider_user';

    // Prevents the "returning id" default behaviour
    protected $primaryKey = 'user_id';

    // Prevents the auto-increment default behaviour
    public $incrementing = false;

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

    /**
     * Get the user that belongs to this SocialProvderUser
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the social provider for the social provider user.
     */
    public function socialProvider(): BelongsTo
    {
        return $this->belongsTo(SocialProvider::class, 'provider_slug', 'slug');
    }
}
