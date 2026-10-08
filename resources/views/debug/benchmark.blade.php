<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Execution Benchmark Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', system-ui, sans-serif; }
        .card { border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-info m-0"><i class="fa-solid fa-stopwatch me-2"></i>Query Execution Benchmark Studio</h2>
                <p class="text-muted small m-0">Measure Average Latency, Min/Max Execution Times over N Iterations</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('debug.dashboard') }}" class="btn btn-outline-primary rounded-pill"><i class="fa-solid fa-gauge me-1"></i>Dashboard</a>
                <a href="{{ route('debug.sandbox') }}" class="btn btn-primary rounded-pill"><i class="fa-solid fa-code me-1"></i>SQL Sandbox</a>
                <a href="{{ route('debug.history') }}" class="btn btn-dark rounded-pill"><i class="fa-solid fa-history me-1"></i>History</a>
            </div>
        </div>

        <div class="card p-4 mb-4">
            <form action="{{ route('debug.benchmark.run') }}" method="POST" class="row g-3 align-items-center">
                @csrf
                <div class="col-md-8">
                    <label class="form-label fw-bold">Select Benchmark Loop Iterations</label>
                    <select name="iterations" class="form-select rounded-pill">
                        <option value="10">10 Iterations (Quick Benchmark)</option>
                        <option value="25" selected>25 Iterations (Standard Benchmark)</option>
                        <option value="50">50 Iterations (Stress Test Benchmark)</option>
                    </select>
                </div>
                <div class="col-md-4 mt-4">
                    <button type="submit" class="btn btn-info text-white rounded-pill w-100 fw-bold"><i class="fa-solid fa-play me-1"></i>Run Benchmark Test</button>
                </div>
            </form>
        </div>

        @if(isset($avgTime))
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card p-4 border-start border-4 border-primary text-center">
                    <div class="text-muted small fw-bold">AVERAGE LATENCY</div>
                    <h3 class="fw-bold text-primary m-0">{{ $avgTime }} ms</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 border-start border-4 border-success text-center">
                    <div class="text-muted small fw-bold">MIN TIME</div>
                    <h3 class="fw-bold text-success m-0">{{ $minTime }} ms</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 border-start border-4 border-warning text-center">
                    <div class="text-muted small fw-bold">MAX TIME</div>
                    <h3 class="fw-bold text-warning m-0">{{ $maxTime }} ms</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 border-start border-4 border-info text-center">
                    <div class="text-muted small fw-bold">PERFORMANCE SCORE</div>
                    <h4 class="fw-bold text-info m-0">{{ $score }}</h4>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fa-solid fa-chart-line text-primary me-2"></i>Benchmark Summary</h5>
            <p>Executed <strong>{{ $iterations }}</strong> loop iterations for query:</p>
            <code class="p-3 bg-light rounded d-block font-monospace mb-3 text-dark border">{{ $sql }}</code>
            <small class="text-muted">Total execution duration: {{ $totalDuration }} ms across all iterations.</small>
        </div>
        @endif
    </div>
</body>
</html>
