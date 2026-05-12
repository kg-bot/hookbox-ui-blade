<?php

declare(strict_types=1);

namespace Hookbox\UiBlade;

use Hookbox\UiBlade\Http\Responses\BladeHookboxUiResponder;
use Hookbox\UiCore\Contracts\HookboxUiResponder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class HookboxUiBladeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/hookbox-ui-blade.php', 'hookbox-ui-blade');

        if (! config('hookbox-ui-blade.enabled', true)) {
            return;
        }

        $this->app->bind(HookboxUiResponder::class, BladeHookboxUiResponder::class);
    }

    public function boot(): void
    {
        $viewsPath = dirname(__DIR__).'/resources/views';

        $this->loadViewsFrom($viewsPath, 'hookbox-ui-blade');

        Blade::anonymousComponentPath($viewsPath.'/components', 'hookbox-ui-blade');

        $this->publishes([
            dirname(__DIR__).'/config/hookbox-ui-blade.php' => config_path('hookbox-ui-blade.php'),
        ], 'hookbox-ui-blade-config');

        $this->publishes([
            $viewsPath => resource_path('views/vendor/hookbox-ui-blade'),
        ], 'hookbox-ui-blade-views');

        if (! config('hookbox-ui-blade.enabled', true)) {
            return;
        }

        $this->loadRoutesFrom(dirname(__DIR__).'/routes/hookbox-ui-blade.php');

    }
}
