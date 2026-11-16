<?php

namespace Devdojo\Auth\Enums;

enum CodeCheckResult: string
{
    case Verified = 'verified';
    case Invalid = 'invalid';
    case Expired = 'expired';
    case TooManyAttempts = 'too_many_attempts';
}
