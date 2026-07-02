<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query History</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }

        .card-stats {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
        }

        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }

        .sql-box {
            max-width: 500px;
            white-space: normal;
            word-break: break-word;
            font-family: monospace;
            font-size: 13px;
        }

        .pagination {
            justify-content: center;
        }
    </style>
</head>

<body>

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">📊 Query History</h2>
                <p class="text-muted mb-0">
                    Search, Manage and Monitor Executed Queries
                </p>
            </div>

            <a href="{{ route('debug.dashboard') }}" class="btn btn-primary">
                ← Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert">
                </button>
            </div>
        @endif

        <div class="row mb-4">

            <div class="col-md-2 mb-3">
                <div class="card card-stats bg-primary text-white">
                    <div class="card-body text-center">
                        <h2>{{ $totalQueries }}</h2>
                        <p class="mb-0">Total</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card card-stats bg-success text-white">
                    <div class="card-body text-center">
                        <h2>{{ $selectQueries }}</h2>
                        <p class="mb-0">SELECT</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card card-stats bg-warning text-dark">
                    <div class="card-body text-center">
                        <h2>{{ $insertQueries }}</h2>
                        <p class="mb-0">INSERT</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card card-stats bg-info text-white">
                    <div class="card-body text-center">
                        <h2>{{ $updateQueries }}</h2>
                        <p class="mb-0">UPDATE</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card card-stats bg-danger text-white">
                    <div class="card-body text-center">
                        <h2>{{ $deleteQueries }}</h2>
                        <p class="mb-0">DELETE</p>
                    </div>
                </div>
            </div>

        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <form method="GET" action="{{ route('debug.history') }}">

                    <div class="row">

                        <div class="col-md-10 mb-2">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search Method, SQL, Query Type, Time..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2 mb-2">
                            <button class="btn btn-dark w-100">
                                Search
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>

        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                <span>Query History Records</span>

                <form method="POST" action="{{ route('debug.history.clear') }}"
                    onsubmit="return confirm('Clear all query history?')">

                    @csrf
                    @method('DELETE')

                    <button class="btn btn-sm btn-danger">
                        Clear All
                    </button>

                </form>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th width="60">ID</th>
                                <th width="120">Method</th>
                                <th>SQL Query</th>
                                <th width="100">Type</th>
                                <th width="120">Time</th>
                                <th width="180">Created</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($histories as $history)

                                <tr>

                                    <td>{{ $history->id }}</td>

                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $history->method }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="sql-box">
                                            {{ $history->sql_query }}
                                        </div>
                                    </td>

                                    <td>
                                        @if($history->query_type == 'SELECT')
                                            <span class="badge bg-success">
                                                SELECT
                                            </span>
                                        @elseif($history->query_type == 'INSERT')
                                            <span class="badge bg-warning text-dark">
                                                INSERT
                                            </span>
                                        @elseif($history->query_type == 'UPDATE')
                                            <span class="badge bg-info">
                                                UPDATE
                                            </span>
                                        @elseif($history->query_type == 'DELETE')
                                            <span class="badge bg-danger">
                                                DELETE
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ $history->query_type }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $history->execution_time }} ms
                                    </td>

                                    <td>
                                        {{ $history->created_at->format('d M Y h:i A') }}
                                    </td>

                                    <td>

                                        <form action="{{ route('debug.history.destroy', $history->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this query?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center py-5">

                                        <h5>No Query History Found</h5>

                                        <p class="text-muted">
                                            Execute Method 1 to Method 5 to generate query history.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if ($histories->lastPage() > 1)
                    <div class="mt-4">
                        <nav>
                            <ul class="pagination justify-content-center">

                                @for ($page = 1; $page <= $histories->lastPage(); $page++)
                                    <li class="page-item {{ $histories->currentPage() == $page ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $histories->appends(request()->query())->url($page) }}">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endfor

                            </ul>
                        </nav>
                    </div>
                @endif

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>