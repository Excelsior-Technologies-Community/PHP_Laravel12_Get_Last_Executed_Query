<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Method 4: Global Query Logging</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding: 20px;
            background: #f8f9fa;
        }

        .query-box {
            background: #fff;
            padding: 12px;
            border-left: 4px solid #ffc107;
            font-family: monospace;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <div class="container">

        <a href="{{ route('debug.dashboard') }}" class="btn btn-secondary mb-3">← Back</a>

        <h2>Method 4: Global Query Logging</h2>
        <p class="text-muted">Tracks all executed queries in request lifecycle</p>

        <div class="card mb-4">
            <div class="card-header bg-warning">
                Executed Queries
            </div>
            <div class="card-body">

                <p>Total Queries: <strong>{{ count($formattedQueries) }}</strong></p>

                @foreach($formattedQueries as $index => $query)
                <div class="query-box">
                    <strong>#{{ $index + 1 }}</strong><br>
                    {{ $query }}
                </div>
                @endforeach

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5>What this shows?</h5>
                <ul>
                    <li>All SELECT / INSERT / UPDATE / DELETE queries</li>
                    <li>Auto captured during request lifecycle</li>
                    <li>Useful for debugging ORM behavior</li>
                </ul>
            </div>
        </div>

    </div>

</body>

</html>