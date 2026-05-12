<?php

declare(strict_types=1);

namespace Hookbox\UiBlade\Tests\Concerns;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait CreatesHookboxRecords
{
    protected function createSource(string $slug, string $name, bool $isActive = true): string
    {
        $sourceId = (string) Str::ulid();
        $timestamp = Carbon::parse('2026-05-09 00:00:00');

        DB::table('hookbox_sources')->insert([
            'id' => $sourceId,
            'slug' => $slug,
            'name' => $name,
            'verifier' => 'Tests\\FakeVerifier',
            'config' => json_encode(['signing_secret' => 'secret'], JSON_THROW_ON_ERROR),
            'is_active' => $isActive,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        return $sourceId;
    }

    protected function createMessage(
        ?string $sourceId,
        string $idempotencyKey,
        string $eventType,
        string $signatureStatus,
        Carbon $receivedAt,
        ?string $clientIp = null,
        ?Carbon $redactedAt = null,
    ): string {
        $messageId = (string) Str::ulid();

        DB::table('hookbox_messages')->insert([
            'id' => $messageId,
            'source_id' => $sourceId,
            'idempotency_key' => $idempotencyKey,
            'event_type' => $eventType,
            'headers' => json_encode(['content-type' => ['application/json']], JSON_THROW_ON_ERROR),
            'body' => json_encode(['id' => $idempotencyKey, 'type' => $eventType], JSON_THROW_ON_ERROR),
            'body_hash' => hash('sha256', $idempotencyKey.$eventType),
            'signature_status' => $signatureStatus,
            'received_at' => $receivedAt,
            'client_ip' => $clientIp,
            'redacted_at' => $redactedAt,
            'created_at' => $receivedAt,
            'updated_at' => $receivedAt,
        ]);

        return $messageId;
    }

    protected function createAttempt(
        string $messageId,
        string $kind,
        string $handler,
        string $status,
        Carbon $startedAt,
        ?Carbon $finishedAt = null,
        ?int $durationMs = null,
        ?string $errorClass = null,
        ?string $errorMessage = null,
        ?string $triggeredBy = null,
    ): string {
        $attemptId = (string) Str::ulid();

        DB::table('hookbox_attempts')->insert([
            'id' => $attemptId,
            'message_id' => $messageId,
            'kind' => $kind,
            'handler' => $handler,
            'status' => $status,
            'started_at' => $startedAt,
            'finished_at' => $finishedAt,
            'duration_ms' => $durationMs,
            'error_class' => $errorClass,
            'error_message' => $errorMessage,
            'error_trace' => $errorMessage === null ? null : 'stack trace',
            'triggered_by' => $triggeredBy,
            'created_at' => $startedAt,
            'updated_at' => $finishedAt ?? $startedAt,
        ]);

        return $attemptId;
    }
}
