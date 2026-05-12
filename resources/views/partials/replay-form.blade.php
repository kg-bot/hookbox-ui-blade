@if ($page['abilities']['replayHookboxMessage'])
    <section class="hb-panel" aria-labelledby="hb-replay-title">
        <h2 id="hb-replay-title">Replay message</h2>

        @if ($page['replay']['defaultsToDryRun'])
            <p class="hb-meta">
                {{ $page['replay']['allowLive'] ? 'Dry run is the default replay mode.' : 'Dry run is the only replay mode currently available.' }}
            </p>
        @endif

        <form method="POST" action="{{ route('hookbox-ui.messages.replay', $page['message']['id']) }}" class="hb-form">
            @csrf

            <div class="hb-field">
                <label for="triggered_by">Triggered By</label>
                <input id="triggered_by" type="text" name="triggered_by" value="{{ is_array(old('triggered_by')) ? '' : old('triggered_by') }}">
                @error('triggered_by')
                    <p class="hb-meta">{{ $message }}</p>
                @enderror
            </div>

            <label class="hb-check">
                <input type="checkbox" name="force_reverify" value="1" @checked(old('force_reverify'))>
                <span>Force reverify</span>
            </label>
            @error('force_reverify')
                <p class="hb-meta">{{ $message }}</p>
            @enderror

            @if ($page['replay']['allowLive'])
                <label class="hb-check">
                    <input type="checkbox" name="live_replay" value="1" @checked(old('live_replay'))>
                    <span>Run live replay</span>
                </label>
                @error('live_replay')
                    <p class="hb-meta">{{ $message }}</p>
                @enderror
            @endif

            <div class="hb-actions">
                <button type="submit" class="hb-button">Replay message</button>
            </div>
        </form>
    </section>
@endif
