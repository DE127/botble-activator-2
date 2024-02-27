<?php

namespace Shaqi\BotbleActivator;

use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;


class Plugin extends PluginOperationAbstract
{
    public static function activated(): void
    {
        // $plugins = get_active_plugins();
        // $isPluginActivated = is_plugin_active('botble-activator');
        //Setting::forceSet('activated_plugins', json_encode($plugins))->save();
    }

    public static function remove(): void
    {
        //
    }
}
