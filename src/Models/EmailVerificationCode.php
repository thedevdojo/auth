<?php

namespace Devdojo\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerificationCode extends Model
{
    protected $table = 'email_verification_codes';

    protected $fillable = ['user_id', 'code_hash', 'attempts', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];
}
