<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Method 3: DB::listen()</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding: 20px;
            background: #f8f9fa;
        }

        .query-box {
            background: #fff;
            padding: 15px;
            border-left: 4px solid #17a2b8;
            font-family: monospace;
            border-radius: 5px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>

    <div class="container">

        <a href="{{ route('debug.dashboard') }}" class="btn btn-secondary mb-3">← Back</a>

        <h2>Method 3: DB::listen()</h2>
        <p class="text-muted">Listens to all executed queries in real-time</p>

        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Last Executed Query
            </div>
            <div class="card-body">

                @if($formattedLastQuery)
                <div class="query-box">
                    <strong>SQL:</strong> {{ $formattedLastQuery['sql'] }} <br>
                    <strong>Time:</strong> {{ $formattedLastQuery['time'] }} <br>
                    <strong>Connection:</strong> {{ $formattedLastQuery['connection'] }}
                </div>
                @else
                <p>No query captured</p>
                @endif

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Sample Output
            </div>
            <div class="card-body">
                <ul>
                    <li>Product fetched (find)</li>
                    <li>Conditional query executed</li>
                    <li>Update query executed</li>
                </ul>

                <p class="text-muted">
                    DB::listen captures every query in real time and logs execution time.
                </p>
            </div>
        </div>

    </div>

</body>

</html>