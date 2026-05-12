<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Feature\Config;

use Hookbox\UiBlade\Tests\TestCase;

final class HookboxUiBladeConfigTest extends TestCase
{
    public function test_route_prefix_can_be_overridden_through_ui_core_config(): void
    {
        $this->refreshHookboxUiConfig(['route_prefix' => 'hookbox-admin']);

        $this->assertSame([
            'hookbox-admin/messages',
            'hookbox-admin/messages/{message}',
            'hookbox-admin/messages/{message}/replay',
        ], $this->hookboxUiBladeRoutes()->map(static fn ($route): string => $route->uri())->all());
    }

    public function test_middleware_can_be_overridden_through_ui_core_config(): void
    {
        $this->refreshHookboxUiConfig(['middleware' => ['web', 'auth.basic']]);

        foreach ($this->hookboxUiBladeRoutes() as $route) {
            $this->assertSame(['web', 'auth.basic'], $route->gatherMiddleware());
        }
    }
}
