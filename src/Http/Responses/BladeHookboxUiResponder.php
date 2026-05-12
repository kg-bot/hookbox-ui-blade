<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Http\Responses;

use Hookbox\UiCore\Contracts\HookboxUiResponder;
use Hookbox\UiCore\ViewModels\InboxPage;
use Hookbox\UiCore\ViewModels\MessageDetailsPage;
use Illuminate\Http\Response;

final class BladeHookboxUiResponder implements HookboxUiResponder
{
    public function forInboxPage(InboxPage $page): Response
    {
        return response()->view('hookbox-ui-blade::pages.inbox', [
            'page' => $page->toArray(),
            'brand' => (string) config('hookbox-ui-blade.brand', 'Hookbox'),
            'title' => (string) config('hookbox-ui-blade.title', 'Hookbox Inbox'),
        ]);
    }

    public function forMessageDetailsPage(MessageDetailsPage $page): Response
    {
        return response()->view('hookbox-ui-blade::pages.message', [
            'page' => $page->toArray(),
            'brand' => (string) config('hookbox-ui-blade.brand', 'Hookbox'),
            'title' => 'Message details',
        ]);
    }
}
