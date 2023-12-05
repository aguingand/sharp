<?php

namespace App\Providers;

use Code16\Sharp\Dev\SharpDevServiceProvider;
use Code16\Sharp\SharpServiceProvider;
use Code16\Sharp\View\Components\Vite as SharpViteComponent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(SharpServiceProvider::class);
//        $this->app->bind(SharpUploadModel::class, Media::class)

        if(class_exists(SharpDevServiceProvider::class)) {
            $this->app->register(SharpDevServiceProvider::class);
        }

        $this->app->bind(SharpViteComponent::class, function () {
            return new SharpViteComponent(hotFile: base_path('../dist/hot'));
        });
    }

    public function boot()
    {
        //
    }
}
