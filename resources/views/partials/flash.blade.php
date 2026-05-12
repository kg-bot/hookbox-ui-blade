@if (session('status'))
    <div class="hb-flash" role="status">{{ session('status') }}</div>
@endif

@if (session('hookbox-ui-blade.replay'))
    <div class="hb-flash" role="status">
        Replay {{ session('hookbox-ui-blade.replay.kind') }} {{ session('hookbox-ui-blade.replay.status') }} via {{ session('hookbox-ui-blade.replay.handler') }}.

        @if (session('hookbox-ui-blade.replay.triggeredBy'))
            Triggered by {{ session('hookbox-ui-blade.replay.triggeredBy') }}.
        @endif
    </div>
@endif
