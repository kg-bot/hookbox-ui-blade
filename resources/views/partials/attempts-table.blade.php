<section class="hb-panel" aria-labelledby="hb-attempts-title">
    <h2 id="hb-attempts-title">Attempts</h2>

    @if ($attempts === [])
        <p class="hb-empty">No delivery attempts have been recorded for this message.</p>
    @else
        <div class="hb-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kind</th>
                        <th>Handler</th>
                        <th>Status</th>
                        <th>Started At</th>
                        <th>Finished At</th>
                        <th>Duration</th>
                        <th>Triggered By</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attempts as $attempt)
                        <tr>
                            <td>{{ $attempt['kind'] }}</td>
                            <td>{{ $attempt['handler'] }}</td>
                            <td><x-hookbox-ui-blade::status-badge :status="$attempt['status']" /></td>
                            <td>{{ $attempt['startedAt'] ?? 'Unknown' }}</td>
                            <td>{{ $attempt['finishedAt'] ?? 'In progress' }}</td>
                            <td>{{ $attempt['durationMs'] === null ? 'Unknown' : $attempt['durationMs'].' ms' }}</td>
                            <td>{{ $attempt['triggeredBy'] ?? 'System' }}</td>
                            <td>
                                @if ($attempt['errorMessage'] !== null)
                                    {{ $attempt['errorClass'] ? $attempt['errorClass'].': ' : '' }}{{ $attempt['errorMessage'] }}
                                @else
                                    None
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
