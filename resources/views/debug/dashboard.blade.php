<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Query Debug Methods</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }

        .method-card {
            margin-bottom: 20px;
        }

        .query-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            font-family: monospace;
            white-space: pre-wrap;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Laravel 12 Query Debugger</h1>
                <p class="lead mb-0">
                    Debug • Analyze • Export SQL Queries
                </p>
            </div>

            <div>
                <a href="{{ route('debug.history') }}"
                    class="btn btn-dark">
                    Query History
                </a>
            </div>
        </div>

        <div class="row mb-4">

            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h2>{{ $totalQueries }}</h2>
                        <h6 class="mt-2 mb-0">Total Queries</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h2>{{ $fastQueries }}</h2>
                        <h6 class="mt-2 mb-0">Fast Queries</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-dark bg-warning">
                    <div class="card-body text-center">
                        <h2>{{ $mediumQueries }}</h2>
                        <h6 class="mt-2 mb-0">Medium Queries</h6>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <h2>{{ $slowQueries }}</h2>
                        <h6 class="mt-2 mb-0">Slow Queries</h6>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card method-card">
                    <div class="card-header bg-primary text-white">
                        <h5>Method 1: DB::getQueryLog()</h5>
                    </div>
                    <div class="card-body">
                        <p>Requires enabling query log first with <code>DB::enableQueryLog()</code></p>
                        <ul>
                            <li>Returns array of all executed queries</li>
                            <li>Get last query with <code>end($queries)</code></li>
                            <li>Includes bindings and execution time</li>
                        </ul>
                        <a href="{{ route('debug.method1') }}" class="btn btn-primary">Try Method 1</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card method-card">
                    <div class="card-header bg-success text-white">
                        <h5>Method 2: toSql() on Builder</h5>
                    </div>
                    <div class="card-body">
                        <p>Get SQL without executing the query</p>
                        <ul>
                            <li>Use <code>$query->toSql()</code></li>
                            <li>Shows SQL with placeholders (?)</li>
                            <li>Use <code>$query->getBindings()</code> to get parameter values</li>
                            <li>Replace bindings manually to generate the complete SQL</li>
                        </ul>
                        <a href="{{ route('debug.method2') }}" class="btn btn-success">
                            Try Method 2
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card method-card">
                    <div class="card-header bg-info text-white">
                        <h5>Method 3: DB::listen()</h5>
                    </div>
                    <div class="card-body">
                        <p>Listen to all queries executed</p>
                        <ul>
                            <li>Global listener for all queries</li>
                            <li>Can log to file or console</li>
                            <li>Includes execution time</li>
                        </ul>
                        <a href="{{ route('debug.method3') }}" class="btn btn-info">Try Method 3</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card method-card">
                    <div class="card-header bg-warning text-dark">
                        <h5>Method 4: Global Query Logging</h5>
                    </div>
                    <div class="card-body">
                        <p>Track executed queries using Laravel Query Log</p>
                        <ul>
                            <li>Captures executed Eloquent queries</li>
                            <li>Stores query history in the database</li>
                            <li>Useful for debugging and query analysis</li>
                        </ul>
                        <a href="{{ route('debug.method4') }}" class="btn btn-warning">
                            Try Method 4
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card method-card">
                    <div class="card-header bg-danger text-white">
                        <h5>Method 5: Raw SQL Queries</h5>
                    </div>
                    <div class="card-body">
                        <p>For raw SQL queries with DB facade</p>
                        <ul>
                            <li>Works with <code>DB::select()</code>, <code>DB::insert()</code>,
                                <code>DB::update()</code>, and <code>DB::delete()</code>
                            </li>
                            <li>Requires query log enabled</li>
                            <li>Same as Method 1 but for raw SQL</li>
                        </ul>
                        <a href="{{ route('debug.method5') }}" class="btn btn-danger">Try Method 5</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Important Notes</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Performance Warning:</strong> Enabling query log (<code>DB::enableQueryLog()</code>) can
                    impact performance in production. Use only for debugging.
                </div>
                <div class="alert alert-info">
                    <strong>For Production Debugging:</strong> Use <code>DB::listen()</code> with conditional logging or
                    a dedicated debugging package like Laravel Debugbar.
                </div>
            </div>
        </div>

        <div class="card mt-4">

            <div class="card-header">
                <h5>Extra Features</h5>
            </div>

            <div class="card-body">

                <div class="d-flex gap-2 flex-wrap">

                    <a href="{{ route('debug.history') }}"
                        class="btn btn-primary">
                        📜 View Query History
                    </a>

                    <a href="{{ route('debug.history.export') }}"
                        class="btn btn-success">
                        📥 Export CSV
                    </a>

                </div>

            </div>

        </div>

        <div class="alert alert-secondary mt-4">

            <h5>Performance Analyzer</h5>

            <hr>

            <div class="mb-2">
                <span class="badge bg-success">
                    Fast
                </span>

                Queries executed in ≤ 20 ms
            </div>

            <div class="mb-2">
                <span class="badge bg-warning text-dark">
                    Medium
                </span>

                Queries executed in 21–80 ms
            </div>

            <div>
                <span class="badge bg-danger">
                    Slow
                </span>

                Queries executed in more than 80 ms
            </div>

        </div>

    </div>
</body>

</html>