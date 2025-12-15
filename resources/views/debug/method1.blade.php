<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Method 1: DB::getQueryLog()</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .query-box { 
            background-color: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            border-left: 4px solid #007bff;
            font-family: monospace;
            white-space: pre-wrap;
        }
        .back-btn { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('debug.dashboard') }}" class="btn btn-secondary back-btn">← Back to Dashboard</a>
        
        <h1>Method 1: Using DB::getQueryLog()</h1>
        <p class="lead">This method requires enabling query log first.</p>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Executed Queries Log</h5>
            </div>
            <div class="card-body">
                <p>Total queries executed: {{ count($queries) }}</p>
                
                <h6>All Queries:</h6>
                @foreach($formattedQueries as $index => $formattedQuery)
                    <div class="query-box mb-2">
                        <strong>Query #{{ $index + 1 }} ({{ $formattedQuery['time'] }}ms):</strong><br>
                        {{ $formattedQuery['sql'] }}
                    </div>
                @endforeach
                
                <h6 class="mt-4">Last Query (using end($queries)):</h6>
                <div class="query-box">
                    <strong>SQL:</strong> {{ $formattedLastQuery }}<br>
                    <strong>Time:</strong> {{ $lastQuery['time'] ?? 'N/A' }}ms<br>
                    <strong>Connection:</strong> {{ $lastQuery['connection'] ?? 'default' }}
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Code Example</h5>
            </div>
            <div class="card-body">
                <pre><code>
// Enable query log first
DB::enableQueryLog();

// Execute your queries
$products = Product::where('price', '>', 100)->get();
$count = Product::count();

// Get all executed queries
$queries = DB::getQueryLog();

// Get the last query
$lastQuery = end($queries);

// Format the last query for display
function formatRawQuery($query) {
    if (!$query) return 'No query executed';
    
    $sql = $query['query'] ?? '';
    $bindings = $query['bindings'] ?? [];
    
    foreach ($bindings as $binding) {
        $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
        $sql = preg_replace('/\?/', $value, $sql, 1);
    }
    
    return $sql;
}
                </code></pre>
            </div>
        </div>
    </div>
</body>
</html>