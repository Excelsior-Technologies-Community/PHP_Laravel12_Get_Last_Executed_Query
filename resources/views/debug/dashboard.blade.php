<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Query Debug Methods</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background-color: #f8f9fa; }
        .method-card { margin-bottom: 20px; }
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
        <h1 class="mb-4">Laravel 12 Query Debugging Methods</h1>
        <p class="lead">Different ways to get the last executed query</p>
        
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
                            <li>Shows SQL with placeholders</li>
                            <li>Need to manually replace bindings</li>
                        </ul>
                        <a href="{{ route('debug.method2') }}" class="btn btn-success">Try Method 2</a>
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
                        <h5>Method 4: Middleware/Global</h5>
                    </div>
                    <div class="card-body">
                        <p>Track all queries globally using middleware</p>
                        <ul>
                            <li>Log all queries automatically</li>
                            <li>Useful for debugging in production</li>
                            <li>Can store in database or log file</li>
                        </ul>
                        <a href="{{ route('debug.method4') }}" class="btn btn-warning">Try Method 4</a>
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
                            <li>Works with <code>DB::select()</code>, <code>DB::insert()</code>, etc.</li>
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
                    <strong>Performance Warning:</strong> Enabling query log (<code>DB::enableQueryLog()</code>) can impact performance in production. Use only for debugging.
                </div>
                <div class="alert alert-info">
                    <strong>For Production Debugging:</strong> Use <code>DB::listen()</code> with conditional logging or a dedicated debugging package like Laravel Debugbar.
                </div>
            </div>
        </div>
    </div>
</body>
</html>