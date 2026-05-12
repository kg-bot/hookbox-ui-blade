@props([
    'status',
])

@php
    $normalizedStatus = strtolower((string) $status);
    $class = match ($normalizedStatus) {
        'valid', 'active' => 'hb-badge-valid',
        'invalid' => 'hb-badge-invalid',
        'inactive' => 'hb-badge-inactive',
        default => 'hb-badge-skipped',
    };
@endphp

<span {{ $attributes->class(['hb-badge', $class]) }}>{{ str_replace('_', ' ', $normalizedStatus) }}</span>
