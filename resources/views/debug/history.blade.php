<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query History & Performance Exporters</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
        }

        .sql-box {
            max-width: 450px;
            white-space: normal;
            word-break: break-all;
            font-family: 'Fira Code', monospace;
            font-size: 12px;
            background: #f8fafc;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>

    <div class="container py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark m-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Query History & Performance Logs</h2>
                <p class="text-muted small m-0">Audit Trail of all executed Eloquent and Raw SQL queries</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('debug.dashboard') }}" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="fa-solid fa-gauge me-1"></i> Dashboard
                </a>
                <a href="{{ route('debug.sandbox') }}" class="btn btn-primary rounded-pill px-3">
                    <i class="fa-solid fa-code me-1"></i> SQL Sandbox
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

        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
        @endif

        <!-- Filter Card -->
        <div class="card p-3 mb-4">
            <form method="GET" action="{{ route('debug.history') }}" class="row g-2">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control rounded-pill" placeholder="🔍 Search Method, SQL Query, Query Type, Time..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="performance" class="form-select rounded-pill">
                        <option value="">All Performance Statuses</option>
                        <option value="Fast" {{ request('performance')=='Fast'?'selected':'' }}>⚡ Fast (≤20ms)</option>
                        <option value="Medium" {{ request('performance')=='Medium'?'selected':'' }}>🚀 Medium (21-80ms)</option>
                        <option value="Slow" {{ request('performance')=='Slow'?'selected':'' }}>🐢 Slow (>80ms)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button class="btn btn-primary rounded-pill w-100">Filter</button>
                    <a href="{{ route('debug.history') }}" class="btn btn-secondary rounded-pill">Reset</a>
                </div>
            </form>
        </div>

        <!-- History Table -->
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-list-ul text-primary me-2"></i>Query Records</h5>
                <form method="POST" action="{{ route('debug.history.clear') }}" onsubmit="return confirm('Clear all query history?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="fa-solid fa-trash me-1"></i>Clear All History</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Method / Source</th>
                            <th>Executed SQL Query</th>
                            <th>Type</th>
                            <th>Latency</th>
                            <th>Performance</th>
                            <th>Recommendation</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                        <tr>
                            <td>#{{ $history->id }}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1">{{ $history->method }}</span></td>
                            <td><div class="sql-box">{{ $history->sql_query }}</div></td>
                            <td>
                                <span class="badge {{ $history->query_type === 'SELECT' ? 'bg-success' : ($history->query_type === 'INSERT' ? 'bg-warning text-dark' : 'bg-info') }}">
                                    {{ $history->query_type }}
                                </span>
                            </td>
                            <td><strong class="font-monospace">{{ $history->execution_time }} ms</strong></td>
                            <td>
                                <span class="badge {{ $history->performance === 'Fast' ? 'bg-success' : ($history->performance === 'Slow' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ $history->performance === 'Fast' ? '⚡ Fast' : ($history->performance === 'Slow' ? '🐢 Slow' : '🚀 Medium') }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ Str::limit($history->recommendation ?? 'Optimal', 30) }}</small></td>
                            <td>
                                <form action="{{ route('debug.history.destroy', $history->id) }}" method="POST" onsubmit="return confirm('Delete record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No query history records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($histories->lastPage() > 1)
            <div class="mt-3 d-flex justify-content-center">
                {{ $histories->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>