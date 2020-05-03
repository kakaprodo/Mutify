<?php
namespace RWBuild\Mutify;

use Illuminate\Support\ServiceProvider;

class MutifyServiceProvider extends ServiceProvider
{
   
    public function register()
    {
        
    }

    
    public function boot()
    {
        $this->registerHelpers();

    }

    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        if (file_exists($file = __DIR__ . '/Http/Utility/helpers.php')) {
            require_once $file;
        }
    }
}