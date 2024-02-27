<?php
namespace Shaqi\BotbleActivator\Providers;

use Illuminate\Routing\Events\RouteMatched;
use Botble\PluginManagement\Events\ActivatedPluginEvent;
use Botble\Base\Http\Middleware\EnsureLicenseHasBeenActivated;
use Shaqi\BotbleActivator\Listeners\SkipLicenseReminderListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        
        ActivatedPluginEvent::class => [
            SkipLicenseReminderListener::class,
        ],
    ];

    public function boot(): void
    {
        $events = $this->app['events'];
        $events->listen(RouteMatched::class, function () {
            $this->app->extend('core.middleware', function ($middleware) {
                // Filter out the middleware you want to remove
                $filteredMiddleware = array_filter($middleware, function ($class) {
                    return $class !== EnsureLicenseHasBeenActivated::class;
                });
                return $filteredMiddleware;
            });
        });
    }

}