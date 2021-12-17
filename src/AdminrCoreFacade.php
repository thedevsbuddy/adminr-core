<?php

namespace Devsbuddy\AdminrCore;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Devsbuddy\AdminrCore\Skeleton\SkeletonClass
 */
class AdminrCoreFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'adminr-core';
    }
}
