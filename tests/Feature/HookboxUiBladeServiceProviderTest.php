<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Feature;

use Hookbox\UiBlade\HookboxUiBladeServiceProvider;
use Hookbox\UiBlade\Http\Responses\BladeHookboxUiResponder;
use Hookbox\UiBlade\Tests\TestCase;
use Hookbox\UiCore\Contracts\HookboxUiResponder;
use Hookbox\UiCore\Http\Responses\JsonHookboxUiResponder;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

final class HookboxUiBladeServiceProviderTest extends TestCase
{
    public function test_package_config_is_merged(): void
    {
        $this->assertTrue(config('hookbox-ui-blade.enabled'));
        $this->assertSame('Hookbox', config('hookbox-ui-blade.brand'));
        $this->assertSame('Hookbox Inbox', config('hookbox-ui-blade.title'));
    }

    public function test_package_config_and_views_are_publishable(): void
    {
        $packageRoot = dirname(__DIR__, 2);
        $configPaths = ServiceProvider::pathsToPublish(HookboxUiBladeServiceProvider::class, 'hookbox-ui-blade-config');
        $viewPaths = ServiceProvider::pathsToPublish(HookboxUiBladeServiceProvider::class, 'hookbox-ui-blade-views');

        $this->assertSame(
            config_path('hookbox-ui-blade.php'),
            $configPaths[$packageRoot.'/config/hookbox-ui-blade.php'] ?? null,
        );

        $this->assertSame(
            resource_path('views/vendor/hookbox-ui-blade'),
            $viewPaths[$packageRoot.'/resources/views'] ?? null,
        );
    }

    public function test_enabled_package_rebinds_responder_during_register(): void
    {
        $this->appInstance()->bind(HookboxUiResponder::class, JsonHookboxUiResponder::class);
        $this->appInstance()['config']->set('hookbox-ui-blade.enabled', true);

        (new HookboxUiBladeServiceProvider($this->appInstance()))->register();

        $this->assertInstanceOf(BladeHookboxUiResponder::class, $this->appInstance()->make(HookboxUiResponder::class));
    }

    public function test_package_registers_views_and_anonymous_component_path(): void
    {
        $this->refreshHookboxUiBladeConfig(['enabled' => true]);

        $compiler = $this->appInstance()->make('blade.compiler');

        $this->assertInstanceOf(BladeCompiler::class, $compiler);
        $this->assertTrue(
            collect($compiler->getAnonymousComponentPaths())->contains(
                fn (array $path): bool => ($path['path'] ?? null) === dirname(__DIR__, 2).'/resources/views/components'
                    && ($path['prefix'] ?? null) === 'hookbox-ui-blade',
            ),
        );
        $this->assertInstanceOf(ViewContract::class, app('view')->make('hookbox-ui-blade::pages.inbox'));
    }

    public function test_disabled_package_keeps_json_responder(): void
    {
        $this->appInstance()->bind(HookboxUiResponder::class, JsonHookboxUiResponder::class);
        $this->appInstance()['config']->set('hookbox-ui-blade.enabled', false);

        (new HookboxUiBladeServiceProvider($this->appInstance()))->register();

        $this->assertInstanceOf(JsonHookboxUiResponder::class, $this->appInstance()->make(HookboxUiResponder::class));
    }
}
