<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests;

use Hookbox\HookboxServiceProvider;
use Hookbox\UiBlade\HookboxUiBladeServiceProvider;
use Hookbox\UiCore\HookboxUiCoreServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionClass;

abstract class TestCase extends Orchestra
{
    /**
     * @var array<string, mixed>
     */
    protected array $hookboxUiConfigOverrides = ['enabled' => false];

    /**
     * @var array<string, mixed>
     */
    protected array $hookboxUiBladeConfigOverrides = [];

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            HookboxServiceProvider::class,
            HookboxUiCoreServiceProvider::class,
            HookboxUiBladeServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('session.driver', 'array');
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $app['config']->set('hookbox-ui', $this->mergeConfig(
            $app['config']->get('hookbox-ui', []),
            $this->hookboxUiConfigOverrides,
        ));

        $app['config']->set('hookbox-ui-blade', $this->mergeConfig(
            $app['config']->get('hookbox-ui-blade', []),
            $this->hookboxUiBladeConfigOverrides,
        ));
    }

    protected function appInstance(): Application
    {
        return $this->app ?? throw new \RuntimeException('Application has not been booted.');
    }

    protected function loadHookboxMigrations(): void
    {
        $hookboxProviderPath = (new ReflectionClass(HookboxServiceProvider::class))->getFileName();

        if ($hookboxProviderPath === false) {
            throw new \RuntimeException('Unable to locate Hookbox service provider path.');
        }

        $this->loadMigrationsFrom(dirname($hookboxProviderPath, 2).'/database/migrations');
    }

    /**
     * @return Collection<int, Route>
     */
    protected function hookboxUiBladeRoutes(): Collection
    {
        return collect($this->appInstance()['router']->getRoutes()->getRoutes())
            ->filter(static fn (Route $route): bool => str_starts_with((string) $route->getName(), 'hookbox-ui.'))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function refreshHookboxUiConfig(array $overrides = []): void
    {
        $this->hookboxUiConfigOverrides = $this->mergeConfig($this->hookboxUiConfigOverrides, $overrides);

        $this->refreshApplication();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function refreshHookboxUiBladeConfig(array $overrides = []): void
    {
        $this->hookboxUiBladeConfigOverrides = $this->mergeConfig($this->hookboxUiBladeConfigOverrides, $overrides);

        $this->refreshApplication();
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function mergeConfig(array $base, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key]) && ! array_is_list($value) && ! array_is_list($base[$key])) {
                /** @var array<string, mixed> $nestedBase */
                $nestedBase = $base[$key];
                /** @var array<string, mixed> $nestedValue */
                $nestedValue = $value;
                $base[$key] = $this->mergeConfig($nestedBase, $nestedValue);

                continue;
            }

            $base[$key] = $value;
        }

        return $base;
    }
}
