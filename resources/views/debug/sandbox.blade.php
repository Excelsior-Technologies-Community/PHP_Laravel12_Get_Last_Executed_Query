<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live SQL Sandbox & EXPLAIN Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', system-ui, sans-serif; }
        .card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .sql-editor { font-family: 'Fira Code', 'Courier New', monospace; font-size: 14px; background: #1e293b; color: #38bdf8; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary m-0"><i class="fa-solid fa-code text-warning me-2"></i>Live SQL Sandbox & EXPLAIN Analyzer Studio</h2>
                <p class="text-muted small m-0">Write custom SELECT queries, analyze execution plans & missing indexes</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('debug.dashboard') }}" class="btn btn-outline-primary rounded-pill"><i class="fa-solid fa-gauge me-1"></i>Dashboard</a>
                <a href="{{ route('debug.n1') }}" class="btn btn-warning rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i>N+1 Detector</a>
                <a href="{{ route('debug.benchmark') }}" class="btn btn-info rounded-pill text-white"><i class="fa-solid fa-stopwatch me-1"></i>Benchmark</a>
                <a href="{{ route('debug.history') }}" class="btn btn-dark rounded-pill"><i class="fa-solid fa-history me-1"></i>History</a>
            </div>
        </div>

        @if(isset($error) && $error)
            <div class="alert alert-danger rounded-3 mb-4"><i class="fa-solid fa-circle-exclamation me-2"></i>{{ $error }}</div>
        @endif

        <div class="card p-4 mb-4">
            <form action="{{ route('debug.sandbox.execute') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fa-solid fa-terminal me-2 text-primary"></i>Enter Custom SQL Query (SELECT only)</label>
                    <textarea name="sql" class="form-control sql-editor p-3" rows="4">{{ $defaultSql }}</textarea>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="fa-solid fa-shield-halved me-1 text-success"></i>Safe Execution Sandbox Mode</span>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="fa-solid fa-play me-1"></i>Run SQL Query & EXPLAIN</button>
                </div>
            </form>
        </div>

        @if(isset($results))
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card p-4 border-start border-4 border-primary">
                    <div class="text-muted small fw-bold">EXECUTION LATENCY</div>
                    <h3 class="fw-bold text-primary m-0">{{ $executionTime }} ms</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 border-start border-4 border-success">
                    <div class="text-muted small fw-bold">RETURNED ROWS</div>
                    <h3 class="fw-bold text-success m-0">{{ count($results) }} rows</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 border-start border-4 border-warning">
                    <div class="text-muted small fw-bold">OPTIMIZATION ADVICE</div>
                    <small class="fw-bold text-dark">{{ $recommendation }}</small>
                </div>
            </div>
        </div>

        <!-- Query Output Dataset Table -->
        <div class="card p-4 mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fa-solid fa-table me-2 text-primary"></i>Query Result Dataset</h5>
            @if(count($results) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            @foreach(array_keys((array)$results[0]) as $col)
                                <th>{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $row)
                        <tr>
                            @foreach((array)$row as $val)
                                <td>{{ $val }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="text-muted m-0">No rows returned by query.</p>
            @endif
        </div>
        @endif
    </div>
</body>
</html>
