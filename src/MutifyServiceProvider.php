<?php
namespace RWBuild\Mutify;

use RWBuild\Mutify\MurugoNotification;
use Illuminate\Support\ServiceProvider;

class MutifyServiceProvider extends ServiceProvider
{
   
    public function register()
    {
        $this->app->bind('Mutify', MurugoNotification::class);
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

        if (file_exists($file = __DIR__ . '/Http/Utility/mutify_exception_helper.php')) {
            require_once $file;
        }
    }
}