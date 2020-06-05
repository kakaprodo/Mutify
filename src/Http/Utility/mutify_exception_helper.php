<?php

use RWBuild\Mutify\Exceptions\GeneralException;

/*
|--------------------------------------------------------------------------
| exception helpers
|--------------------------------------------------------------------------
|
| Here is where all exception helper functions will be defined 
|--------------------------------------------------------------------------
*/


/**
 * fire an exception in response format
 */
function mutifyErr($error_msg,$status = 400) {
    throw new GeneralException($error_msg, $status);
}
 
/**
 * fire an exception in response format with other payload data
 */
function  mutifyErrWith($error_msg) {
    return new GeneralException($error_msg);
}
