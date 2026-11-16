<?php

namespace Devdojo\Auth;

use Devdojo\Auth\Skeleton\SkeletonClass;
use Illuminate\Support\Facades\Facade;

/**
 * @see SkeletonClass
 */
class AuthFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth';
    }
}
