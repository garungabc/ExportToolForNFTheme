<?php

namespace Vicoders\Notification\Facades;

use Illuminate\Support\Facades\Facade;

class View extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new \Vicoders\Notification\Services\View;
    }
}
