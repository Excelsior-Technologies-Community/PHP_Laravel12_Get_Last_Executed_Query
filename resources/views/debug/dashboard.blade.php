<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 12 Query Debugger & Optimizer Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', system-ui, sans-serif; }
        .card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .method-card { transition: transform 0.2s ease; }
        .method-card:hover { transform: translateY(-3px); }
    </style>
</head>

<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-primary m-0"><i class="fa-solid fa-database text-warning me-2"></i>Laravel 12 Query Debugger & EXPLAIN Studio</h2>
                <p class="text-muted small m-0">Debug, Analyze, Profile, Benchmark & Export Database Queries</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('debug.sandbox') }}" class="btn btn-primary rounded-pill px-3">
                    <i class="fa-solid fa-code me-1"></i> SQL Sandbox
                </a>
                <a href="{{ route('debug.n1') }}" class="btn btn-warning rounded-pill px-3">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> N+1 Detector
                </a>
                <a href="{{ route('debug.benchmark') }}" class="btn btn-info rounded-pill px-3 text-white">
                    <i class="fa-solid fa-stopwatch me-1"></i> Benchmark
                </a>
                <a href="{{ route('debug.history') }}" class="btn btn-dark rounded-pill px-3">
                    <i class="fa-solid fa-history me-1"></i> History
                </a>
                <div class="btn-group">
                    <button class="btn btn-outline-success dropdown-toggle rounded-pill px-3" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-file-export me-1"></i> Exporter Studio
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a href="{{ route('debug.history.export') }}" class="dropdown-item"><i class="fa-solid fa-file-csv text-success me-2"></i> Export CSV</a></li>
                        <li><a href="{{ route('debug.history.export.pdf') }}" target="_blank" class="dropdown-item"><i class="fa-solid fa-file-pdf text-danger me-2"></i> Print / PDF Report</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="{{ route('debug.api.json') }}" target="_blank" class="dropdown-item"><i class="fa-solid fa-code text-info me-2"></i> Restful JSON API</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Dashboard Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card p-3 border-start border-4 border-primary text-center">
                    <h3 class="fw-bold text-primary m-0">{{ $totalQueries }}</h3>
                    <small class="text-muted fw-bold">TOTAL LOGGED QUERIES</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-start border-4 border-success text-center">
                    <h3 class="fw-bold text-success m-0">{{ $fastQueries }}</h3>
                    <small class="text-muted fw-bold">FAST QUERIES (≤20ms)</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-start border-4 border-warning text-center">
                    <h3 class="fw-bold text-warning m-0">{{ $mediumQueries }}</h3>
                    <small class="text-muted fw-bold">MEDIUM QUERIES (21-80ms)</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 border-start border-4 border-danger text-center">
                    <h3 class="fw-bold text-danger m-0">{{ $slowQueries }}</h3>
                    <small class="text-muted fw-bold">SLOW QUERIES (>80ms)</small>
                </div>
            </div>
        </div>

        <!-- 5 Query Debugging Methods Grid -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card method-card p-4">
                    <h5 class="fw-bold text-primary"><i class="fa-solid fa-1 me-2"></i>Method 1: DB::getQueryLog()</h5>
                    <p class="text-muted small">Requires enabling query log with <code>DB::enableQueryLog()</code> to capture executed queries and latency.</p>
                    <a href="{{ route('debug.method1') }}" class="btn btn-outline-primary rounded-pill">Try Method 1</a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card method-card p-4">
                    <h5 class="fw-bold text-success"><i class="fa-solid fa-2 me-2"></i>Method 2: toSql() on Builder</h5>
                    <p class="text-muted small">Inspect SQL string without executing query using <code>$query->toSql()</code> and bindings replacement.</p>
                    <a href="{{ route('debug.method2') }}" class="btn btn-outline-success rounded-pill">Try Method 2</a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card method-card p-4">
                    <h5 class="fw-bold text-info"><i class="fa-solid fa-3 me-2"></i>Method 3: DB::listen()</h5>
                    <p class="text-muted small">Global listener for all database query executions with real-time logging.</p>
                    <a href="{{ route('debug.method3') }}" class="btn btn-outline-info rounded-pill">Try Method 3</a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card method-card p-4">
                    <h5 class="fw-bold text-warning"><i class="fa-solid fa-4 me-2"></i>Method 4: Global Query Logging</h5>
                    <p class="text-muted small">Captures executed Eloquent queries globally and records audit trail into database.</p>
                    <a href="{{ route('debug.method4') }}" class="btn btn-outline-warning rounded-pill">Try Method 4</a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card method-card p-4">
                    <h5 class="fw-bold text-danger"><i class="fa-solid fa-5 me-2"></i>Method 5: Raw SQL Queries</h5>
                    <p class="text-muted small">Inspects raw SQL statements executed using <code>DB::select()</code> and <code>DB::statement()</code>.</p>
                    <a href="{{ route('debug.method5') }}" class="btn btn-outline-danger rounded-pill">Try Method 5</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>