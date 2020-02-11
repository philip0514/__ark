<?php

namespace Philip0514\Ark\Facade;

use Illuminate\Support\Facades\Facade;

class Ark extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ark';
    }
}