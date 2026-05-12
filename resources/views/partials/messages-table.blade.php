<div class="hb-table-wrap">
    <table>
        <thead>
            <tr>
                <th>Source</th>
                <th>Event Type</th>
                <th>Message Reference</th>
                <th>Signature Status</th>
                <th>Received At</th>
                <th>Client IP</th>
                <th>Redacted</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>
                        <a href="{{ route('hookbox-ui.messages.show', $row['id']) }}">{{ $row['sourceName'] ?? $row['sourceSlug'] ?? 'Unknown' }}</a>
                    </td>
                    <td>{{ $row['eventType'] }}</td>
                    <td>{{ $row['idempotencyKey'] }}</td>
                    <td><x-hookbox-ui-blade::status-badge :status="$row['signatureStatus']" /></td>
                    <td>{{ $row['receivedAt'] }}</td>
                    <td>{{ $row['clientIp'] ?? 'Unknown' }}</td>
                    <td>{{ $row['isRedacted'] ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
