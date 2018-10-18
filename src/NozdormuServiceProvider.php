<?php

namespace TinyBox\Nozdormu;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->registerConsoleCommands();
        }
    }

    private function registerConsoleCommands()
    {
        $this->commands(Commands\BackupUser::class);
        $this->commands(Commands\BackupDBData::class);
        $this->commands(Commands\BackupMenu::class);
        $this->commands(Commands\BackupSetting::class);
        $this->commands(Commands\BackupAll::class);
    }
}
