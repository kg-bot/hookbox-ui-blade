# Installation

`kg-bot/hookbox-ui-blade` is the Blade adapter layer. `kg-bot/hookbox-ui-core` remains the shared base package that defines the normalized page contract, shared route settings, and replay safety rules, and UI Core depends on `kg-bot/hookbox` for the underlying Hookbox data and replay services.

Install the adapter package:

```bash
composer require kg-bot/hookbox-ui-blade
```

Then publish the shared UI Core config, the Blade adapter config, and the Blade views:

```bash
php artisan vendor:publish --tag=hookbox-ui-config
php artisan vendor:publish --tag=hookbox-ui-blade-config
php artisan vendor:publish --tag=hookbox-ui-blade-views
```

## Why Two Config Files?

- `config/hookbox-ui.php` belongs to UI Core and controls shared route prefix, middleware, pagination, and replay safety rules.
- `config/hookbox-ui-blade.php` belongs to this adapter and controls whether the Blade adapter is enabled plus simple presentation settings like brand and title.

When you use the Blade adapter, disable the UI Core route registration in `config/hookbox-ui.php`. That prevents route collisions because both packages use the same `hookbox-ui.*` route names and the same shared URL structure.

## Example Shared UI Core Config

```php
<?php

return [
    'enabled' => false,
    'route_prefix' => 'hookbox',
    'middleware' => ['web'],
    'pagination' => [
        'per_page' => 25,
    ],
    'replay' => [
        'allow_live' => true,
    ],
];
```

## Example Blade Adapter Config

```php
<?php

return [
    'enabled' => true,
    'brand' => 'Hookbox',
    'title' => 'Hookbox Inbox',
];
```

## Install Path Summary

1. Install `kg-bot/hookbox-ui-blade`.
2. Publish `hookbox-ui-config`, `hookbox-ui-blade-config`, and `hookbox-ui-blade-views`.
3. Set `config/hookbox-ui.php` to `'enabled' => false` so the Blade adapter owns the UI routes.
4. Keep `config/hookbox-ui-blade.php` enabled.
5. Optionally customize published views and Blade config values. No frontend asset pipeline is required.
