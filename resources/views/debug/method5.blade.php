<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Method 5: Raw SQL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding: 20px;
            background: #f8f9fa;
        }

        .query-box {
            background: #fff;
            padding: 15px;
            border-left: 4px solid #dc3545;
            font-family: monospace;
            border-radius: 5px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>

    <div class="container">

        <a href="{{ route('debug.dashboard') }}" class="btn btn-secondary mb-3">← Back</a>

        <h2>Method 5: Raw SQL Query Debugging</h2>

        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                Last Executed Raw Query
            </div>

            <div class="card-body">

                <div class="query-box">
                    {{ $formattedQuery }}
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Query Log Count
            </div>
            <div class="card-body">
                <p>Total Queries Captured: <strong>{{ count($queries) }}</strong></p    >

                <ul>
                    <li>Raw DB::select() query executed</li>
                    <li>Parameter binding supported</li>
                    <li>Useful for performance testing</li>
                </ul>
            </div>
        </div>

    </div>

</body>

</html>