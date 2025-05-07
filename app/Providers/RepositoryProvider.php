<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $interfacePath = app_path(). "/Interfaces";
        $repositoryFiles = array_diff(scandir(app_path() . "/Repositories"), array('.', '..'));

        foreach ($repositoryFiles as $repositoryFile) {
            if (file_exists($interfacePath . "/" . str_replace('.php', 'Interface.php', $repositoryFile))) {
                $repositoryClass =  "App\Repositories\\" . str_replace('.php', '', $repositoryFile);
                $interfaceClass =  "App\Interfaces\\" . str_replace('.php', 'Interface', $repositoryFile);
                $this->app->bind($interfaceClass, $repositoryClass);
            }
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
