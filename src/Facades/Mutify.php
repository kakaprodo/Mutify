<?php
namespace RWBuild\Mutify\Facades;

use Illuminate\Support\Facades\Facade;

class Mutify extends Facade {

    protected static function getFacadeAccessor(){

        return 'Mutify';
    }
}