<?php

namespace Shaqi\BotbleActivator\Providers;

use Illuminate\Support\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class ShaqiActivatorProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this->setNamespace('plugins/botble-activator')
            ->loadRoutes();

        $this->app->register(EventServiceProvider::class);    
    }
    
}
