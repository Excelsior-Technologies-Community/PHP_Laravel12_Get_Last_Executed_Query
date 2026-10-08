<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>N+1 Query Detector & Alert Studio</title>
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
                <h2 class="fw-bold text-danger m-0"><i class="fa-solid fa-triangle-exclamation me-2"></i>N+1 Query Detector & Performance Radar</h2>
                <p class="text-muted small m-0">Detect redundant database queries in loops & optimize eager loading</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('debug.dashboard') }}" class="btn btn-outline-primary rounded-pill"><i class="fa-solid fa-gauge me-1"></i>Dashboard</a>
                <a href="{{ route('debug.sandbox') }}" class="btn btn-primary rounded-pill"><i class="fa-solid fa-code me-1"></i>SQL Sandbox</a>
                <a href="{{ route('debug.history') }}" class="btn btn-dark rounded-pill"><i class="fa-solid fa-history me-1"></i>History</a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card p-4 border-top border-4 border-danger">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-danger m-0"><i class="fa-solid fa-bug me-2"></i>Without Eager Loading (N+1 Risk)</h5>
                        <span class="badge bg-danger rounded-pill fs-6">{{ $queriesN1Count }} Queries</span>
                    </div>
                    <p class="text-muted small">Executing relationship access inside <code>@@foreach</code> loop without <code>with()</code> relationship loading triggers separate queries for every iteration.</p>
                    <div class="bg-light p-3 rounded font-monospace small text-dark border">
                        @foreach($n1Products->take(3) as $p)
                            <div>Product #{{ $p->id }}: {{ $p->name }} (Price: ${{ $p->price }})</div>
                        @endforeach
                    </div>
                    <div class="alert alert-danger mt-3 mb-0 small">
                        <strong>⚠️ Recommendation:</strong> Use Eager Loading <code>Product::with('category')->get()</code> to eliminate N+1 latency.
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-4 border-top border-4 border-success">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-bolt me-2"></i>Optimized Query Strategy</h5>
                        <span class="badge bg-success rounded-pill fs-6">{{ $queriesOptimizedCount }} Query</span>
                    </div>
                    <p class="text-muted small">Optimized batch query retrieves all matching records in a single database round-trip.</p>
                    <div class="bg-light p-3 rounded font-monospace small text-dark border">
                        @foreach($optimizedProducts->take(3) as $p)
                            <div>Product #{{ $p->id }}: {{ $p->name }} (Price: ${{ $p->price }})</div>
                        @endforeach
                    </div>
                    <div class="alert alert-success mt-3 mb-0 small">
                        <strong>✅ Result:</strong> 100% Query efficiency optimization achieved!
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
