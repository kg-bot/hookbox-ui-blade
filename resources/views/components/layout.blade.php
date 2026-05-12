@props([
    'title',
    'brand',
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: ui-sans-serif, system-ui, sans-serif; background: #f5f7fb; color: #172033; }
        a { color: #1d4ed8; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .hb-shell { max-width: 1200px; margin: 0 auto; padding: 24px 16px 40px; }
        .hb-header { display: flex; justify-content: space-between; align-items: baseline; gap: 16px; margin-bottom: 24px; }
        .hb-brand { margin: 0; font-size: 0.95rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #475569; }
        .hb-title { margin: 4px 0 0; font-size: 2rem; }
        .hb-card, .hb-panel, .hb-table-wrap { background: #fff; border: 1px solid #dbe3f0; border-radius: 12px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04); }
        .hb-grid { display: grid; gap: 16px; }
        .hb-grid-metrics { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 16px; }
        .hb-grid-sources { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 16px; }
        .hb-card { padding: 16px; }
        .hb-source-card-header { display: flex; justify-content: space-between; gap: 12px; align-items: center; }
        .hb-source-card-copy { min-width: 0; }
        .hb-source-card-title { margin: 0; font-size: 1.1rem; }
        .hb-source-card-slug { margin-top: 4px; }
        .hb-kicker { margin: 0 0 8px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
        .hb-value { margin: 0; font-size: 1.8rem; font-weight: 700; }
        .hb-meta { margin: 6px 0 0; color: #475569; }
        .hb-panel { padding: 16px; margin-bottom: 16px; }
        .hb-panel h2 { margin: 0 0 12px; font-size: 1.05rem; }
        .hb-flash { padding: 12px 14px; margin-bottom: 16px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; color: #166534; }
        .hb-form { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); }
        .hb-field { display: grid; gap: 6px; }
        .hb-field label { font-size: 0.9rem; font-weight: 600; color: #334155; }
        .hb-field input, .hb-field select { width: 100%; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; }
        .hb-actions { display: flex; align-items: end; gap: 10px; }
        .hb-button { display: inline-flex; align-items: center; justify-content: center; padding: 10px 14px; border: 1px solid #1d4ed8; border-radius: 8px; background: #1d4ed8; color: #fff; font-weight: 600; cursor: pointer; }
        .hb-button-secondary { background: #fff; color: #1d4ed8; }
        .hb-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; text-transform: capitalize; }
        .hb-badge-valid { background: #dcfce7; color: #166534; }
        .hb-badge-invalid { background: #fee2e2; color: #991b1b; }
        .hb-badge-skipped { background: #e2e8f0; color: #334155; }
        .hb-badge-active { background: #dbeafe; color: #1e3a8a; }
        .hb-badge-inactive { background: #f1f5f9; color: #475569; }
        .hb-table-wrap { overflow-x: auto; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; }
        tr:last-child td { border-bottom: 0; }
        .hb-empty { padding: 24px; margin: 0; background: #fff; border: 1px dashed #cbd5e1; border-radius: 12px; color: #475569; text-align: center; }
        .hb-pagination { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; justify-content: space-between; }
        .hb-pagination-links { display: flex; flex-wrap: wrap; gap: 8px; }
        .hb-page-link { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #1e293b; }
        .hb-page-link[aria-current="page"] { border-color: #1d4ed8; background: #dbeafe; color: #1e3a8a; font-weight: 700; }
        @media (max-width: 640px) { .hb-header { flex-direction: column; } .hb-shell { padding: 16px 12px 32px; } th, td { padding: 10px 12px; } }
    </style>
</head>
<body>
    <main class="hb-shell">
        <header class="hb-header">
            <div>
                <p class="hb-brand">{{ $brand }}</p>
                <h1 class="hb-title">{{ $title }}</h1>
            </div>
        </header>

        {{ $slot }}
    </main>
</body>
</html>
