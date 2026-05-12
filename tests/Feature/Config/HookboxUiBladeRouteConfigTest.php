<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Feature\Config;

use Hookbox\UiBlade\Http\Controllers\ReplayController;
use Hookbox\UiBlade\Tests\TestCase;
use Hookbox\UiCore\Http\Controllers\InboxController;
use Hookbox\UiCore\Http\Controllers\MessageController;

final class HookboxUiBladeRouteConfigTest extends TestCase
{
    public function test_routes_register_under_the_shared_hookbox_prefix_by_default(): void
    {
        $routes = $this->hookboxUiBladeRoutes();

        $this->assertSame([
            'hookbox/messages',
            'hookbox/messages/{message}',
            'hookbox/messages/{message}/replay',
        ], $routes->map(static fn ($route): string => $route->uri())->all());

        $this->assertSame([
            ['GET', 'HEAD'],
            ['GET', 'HEAD'],
            ['POST'],
        ], $routes->map(static fn ($route): array => $route->methods())->all());

        $this->assertSame([
            'hookbox-ui.messages.index',
            'hookbox-ui.messages.show',
            'hookbox-ui.messages.replay',
        ], $routes->map(static fn ($route): ?string => $route->getName())->all());

        $this->assertSame([
            InboxController::class,
            MessageController::class,
            ReplayController::class,
        ], $routes->map(static fn ($route): ?string => $route->getControllerClass())->all());

        $this->assertSame([
            InboxController::class.'@__invoke',
            MessageController::class.'@__invoke',
            ReplayController::class.'@__invoke',
        ], $routes->map(static fn ($route): string => $route->getAction()['uses'])->all());

        $this->assertSame([
            '__invoke',
            '__invoke',
            '__invoke',
        ], $routes->map(static fn ($route): string => str($route->getAction()['uses'])->afterLast('@')->toString())->all());
    }

    public function test_route_prefix_and_middleware_follow_the_ui_core_config_overrides(): void
    {
        $this->refreshHookboxUiConfig([
            'route_prefix' => 'hookbox-admin',
            'middleware' => ['web', 'auth.basic'],
        ]);

        $routes = $this->hookboxUiBladeRoutes();

        $this->assertCount(3, $routes);

        foreach ($routes as $route) {
            $this->assertStringStartsWith('hookbox-admin/', $route->uri());
            $this->assertSame(['web', 'auth.basic'], $route->gatherMiddleware());
        }
    }

    public function test_routes_are_disabled_when_the_blade_package_is_disabled(): void
    {
        $this->refreshHookboxUiBladeConfig(['enabled' => false]);

        $this->assertCount(0, $this->hookboxUiBladeRoutes());
    }

    public function test_blade_replay_route_is_preserved_when_ui_core_routes_are_enabled(): void
    {
        $this->refreshHookboxUiConfig(['enabled' => true]);

        $replayRoute = $this->appInstance()['router']->getRoutes()->getByName('hookbox-ui.messages.replay');

        $this->assertNotNull($replayRoute);
        $this->assertSame(
            ReplayController::class.'@__invoke',
            $replayRoute->getAction()['uses'],
        );
    }
}
