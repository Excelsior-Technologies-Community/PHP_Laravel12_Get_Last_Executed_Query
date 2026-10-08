<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Query Performance & Audit Report</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fff; color: #333; margin: 30px; }
        .header { text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 15px; margin-bottom: 25px; }
        .header h2 { margin: 0; color: #0d6efd; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; font-size: 12px; }
        th { background: #f1f5f9; color: #1e293b; font-weight: 600; }
        tr:nth-child(even) { background: #f8fafc; }
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; color: #fff; }
        .badge-fast { background: #198754; }
        .badge-medium { background: #ffc107; color: #000; }
        .badge-slow { background: #dc3545; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>⚡ Database Query Performance & Audit Report</h2>
        <p>Generated on {{ date('d M Y, h:i A') }} | Total Queries: {{ $histories->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Method / Source</th>
                <th>Executed SQL Query</th>
                <th>Type</th>
                <th>Execution Time</th>
                <th>Performance</th>
                <th>Recommendation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $history)
            <tr>
                <td>#{{ $history->id }}</td>
                <td><strong>{{ $history->method }}</strong></td>
                <td><code style="word-break: break-all;">{{ $history->sql_query }}</code></td>
                <td>{{ $history->query_type }}</td>
                <td>{{ $history->execution_time }} ms</td>
                <td>
                    <span class="badge badge-{{ strtolower($history->performance) }}">
                        {{ $history->performance }}
                    </span>
                </td>
                <td>{{ $history->recommendation ?? 'Optimal' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Laravel 12 SQL Query Optimizer & EXPLAIN Analyzer Studio
    </div>

</body>
</html>
