<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Flutterwave extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'flutterwave';
    }
}
