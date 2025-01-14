<?php

namespace Olivion\RestGen\Facades;

use Illuminate\Support\Facades\Facade;

class RestGen extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'rest-gen';
    }
}
