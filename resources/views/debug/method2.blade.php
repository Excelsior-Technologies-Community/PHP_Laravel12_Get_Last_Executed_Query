<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Method 2: toSql() on Builder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .query-box { 
            background-color: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            border-left: 4px solid #28a745;
            font-family: monospace;
            white-space: pre-wrap;
        }
        .back-btn { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('debug.dashboard') }}" class="btn btn-secondary back-btn">← Back to Dashboard</a>
        
        <h1>Method 2: Using toSql() on Eloquent Builder</h1>
        <p class="lead">Get SQL without executing or with bindings replaced.</p>
        
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5>Query Results</h5>
            </div>
            <div class="card-body">
                <p>Found {{ $products->count() }} products with price > 100</p>
                
                <h6>Raw SQL (with ? placeholders):</h6>
                <div class="query-box mb-3">
                    {{ $rawSql }}
                </div>
                
                <h6>SQL with Bindings Replaced:</h6>
                <div class="query-box">
                    {{ $sqlWithBindings }}
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Code Example</h5>
            </div>
            <div class="card-body">
                <pre><code>
$query = Product::where('price', '>', 100);

// Get SQL with placeholders
$rawSql = $query->toSql();
// Returns: "select * from `products` where `price` > ?"

// Get bindings
$bindings = $query->getBindings();

// Manually replace bindings for full SQL
$sqlWithBindings = $query->toSql();
foreach ($bindings as $binding) {
    $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
    $sqlWithBindings = preg_replace('/\?/', $value, $sqlWithBindings, 1);
}
// Returns: "select * from `products` where `price` > 100"

// Execute the query
$products = $query->get();
                </code></pre>
            </div>
        </div>
    </div>
</body>
</html>