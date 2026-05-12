<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Http\Controllers;

use Hookbox\ReplayOptions;
use Hookbox\ReplayService;
use Hookbox\UiCore\Http\Requests\ReplayRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

final class ReplayController
{
    use AuthorizesRequests;

    public function __invoke(
        ReplayRequest $request,
        string $message,
        ReplayService $replayService,
    ): RedirectResponse {
        $this->authorize('replayHookboxMessage');

        $attempt = $replayService->replay($message, new ReplayOptions(
            dryRun: ! $request->boolean('live_replay') || ! config('hookbox-ui.replay.allow_live', true),
            triggeredBy: $request->string('triggered_by')->toString() ?: null,
            actionsFilter: $this->actionsFilter($request),
            forceReverify: $request->boolean('force_reverify'),
        ));

        return redirect()
            ->route('hookbox-ui.messages.show', $message)
            ->with([
                'hookbox-ui-blade' => [
                    'replay' => [
                        'attemptId' => (string) $attempt->getKey(),
                        'kind' => (string) $attempt->kind,
                        'status' => (string) $attempt->status,
                        'handler' => (string) $attempt->handler,
                        'triggeredBy' => $attempt->triggered_by,
                    ],
                ],
            ]);
    }

    /**
     * @return list<class-string>|null
     */
    private function actionsFilter(ReplayRequest $request): ?array
    {
        $filter = $request->validated('actions_filter');

        if (! is_array($filter)) {
            return null;
        }

        /** @var list<class-string> $filter */
        return $filter;
    }
}
