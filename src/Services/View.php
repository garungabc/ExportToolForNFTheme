<?php

namespace Vicoders\Notification\Services;

class View
{
    use \NF\Traits\View;
    public function __construct()
    {
        $this->setViewPath(__DIR__ . '/../../resources/views');
        $this->setCachePath(__DIR__ . '/../../resources/cache');
    }
}
