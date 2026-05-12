<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Feature\Boot;

use Hookbox\HookboxServiceProvider;
use Hookbox\UiBlade\HookboxUiBladeServiceProvider;
use Hookbox\UiBlade\Tests\TestCase;
use Hookbox\UiCore\HookboxUiCoreServiceProvider;

final class PackageBootTest extends TestCase
{
    public function test_package_boots_with_hookbox_and_ui_core_loaded(): void
    {
        $this->assertNotNull($this->appInstance()->getProvider(HookboxServiceProvider::class));
        $this->assertNotNull($this->appInstance()->getProvider(HookboxUiCoreServiceProvider::class));
        $this->assertNotNull($this->appInstance()->getProvider(HookboxUiBladeServiceProvider::class));
    }
}
