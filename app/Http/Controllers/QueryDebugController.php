<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QueryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryDebugController extends Controller
{
    // Method 1: Using DB::getQueryLog()
    public function method1()
    {
        DB::enableQueryLog();

        $products = Product::where('price', '>', 100)->get();
        $count = Product::count();
        $expensiveProducts = Product::where('price', '>', 500)->orderBy('price', 'desc')->get();

        $queries = DB::getQueryLog();
        $this->saveQueryHistory('Method 1', $queries);

        $formattedQueries = [];
        foreach ($queries as $query) {
            $formattedQueries[] = [
                'sql' => $this->formatRawQuery($query),
                'time' => $query['time'],
                'connection' => $query['connection'] ?? 'default',
                'raw' => $query
            ];
        }

        $lastQuery = end($queries);
        $formattedLastQuery = $lastQuery ? $this->formatRawQuery($lastQuery) : 'No query executed';

        return view('debug.method1', [
            'products' => $products,
            'count' => $count,
            'expensiveProducts' => $expensiveProducts,
            'queries' => $queries,
            'formattedQueries' => $formattedQueries,
            'lastQuery' => $lastQuery,
            'formattedLastQuery' => $formattedLastQuery,
        ]);
    }

    // Method 2: Using toSql() on Eloquent Builder
    public function method2()
    {
        DB::enableQueryLog();

        $query = Product::where('price', '>', 100);
        $rawSql = $query->toSql();
        $sqlWithBindings = $this->getSqlWithBindings($query);

        $products = $query->get();
        $queries = DB::getQueryLog();

        $this->saveQueryHistory('Method 2', $queries);

        return view('debug.method2', [
            'products' => $products,
            'rawSql' => $rawSql,
            'sqlWithBindings' => $sqlWithBindings,
        ]);
    }

    // Method 3: Using DB::listen()
    public function method3()
    {
        DB::enableQueryLog();

        $lastQuery = null;
        $formattedLastQuery = null;

        DB::listen(function ($query) use (&$lastQuery, &$formattedLastQuery) {
            $lastQuery = $query;
            $formattedLastQuery = $this->formatQuery($query);

            Log::info('Executed Query:', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });

        $product = Product::find(1);
        $cheapProducts = Product::where('price', '<', 100)->get();
        $updated = Product::where('id', 2)->update(['quantity' => 30]);
        $queries = DB::getQueryLog();

        $this->saveQueryHistory('Method 3', $queries);

        return view('debug.method3', [
            'product' => $product,
            'cheapProducts' => $cheapProducts,
            'lastQuery' => $lastQuery,
            'formattedLastQuery' => $formattedLastQuery,
        ]);
    }

    // Method 4: Global Scope / Middleware Logging
    public function method4()
    {
        DB::enableQueryLog();

        $allProducts = Product::all();
        $firstProduct = Product::first();
        $newProduct = Product::create([
            'name' => 'Tablet',
            'description' => 'Latest tablet',
            'price' => 399.99,
            'quantity' => 15,
        ]);

        $queries = DB::getQueryLog();
        $this->saveQueryHistory('Method 4', $queries);
        $formattedQueries = [];
        foreach ($queries as $query) {
            $formattedQueries[] = $this->formatRawQuery($query);
        }

        return view('debug.method4', [
            'allProducts' => $allProducts,
            'firstProduct' => $firstProduct,
            'newProduct' => $newProduct,
            'formattedQueries' => $formattedQueries,
        ]);
    }

    // Method 5: Raw SQL & Last Executed Query
    public function method5()
    {
        DB::enableQueryLog();

        DB::select('SELECT * FROM products WHERE quantity > ?', [20]);

        $queries = DB::getQueryLog();
        $this->saveQueryHistory('Method 5', $queries);
        $lastQuery = end($queries);

        $formattedQuery = $lastQuery ? $this->formatRawQuery($lastQuery) : 'No query executed';

        return view('debug.method5', [
            'lastQuery' => $lastQuery,
            'formattedQuery' => $formattedQuery,
            'queries' => $queries,
        ]);
    }

    /**
     * Interactive Live SQL Sandbox & EXPLAIN Query Analyzer
     */
    public function sandboxIndex()
    {
        $defaultSql = "SELECT * FROM products WHERE price > 100 ORDER BY price DESC;";
        return view('debug.sandbox', compact('defaultSql'));
    }

    public function sandboxExecute(Request $request)
    {
        $sql = trim($request->input('sql', 'SELECT * FROM products;'));

        // Security check: Only allow SELECT queries in Sandbox
        if (!preg_match('/^\s*SELECT/i', $sql)) {
            return back()->with('error', 'Security Policy: Only SELECT queries are permitted in Sandbox mode.');
        }

        $startTime = microtime(true);
        $results = [];
        $error = null;
        $explainResults = [];
        $recommendation = "Optimal query execution.";

        try {
            $results = DB::select($sql);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            // Automated EXPLAIN Plan
            try {
                $explainResults = DB::select("EXPLAIN " . $sql);
                foreach ($explainResults as $explain) {
                    $type = $explain->type ?? $explain->select_type ?? '';
                    if (str_contains(strtoupper($type), 'ALL')) {
                        $recommendation = "⚠️ Warning: Full Table Scan detected! Consider adding an index on WHERE clause columns to optimize performance.";
                    }
                }
            } catch (\Exception $ex) {
                // Explain optional for simple SQLite
            }

            // Save to Query History
            QueryHistory::create([
                'method' => 'Live SQL Sandbox',
                'sql_query' => $sql,
                'query_type' => 'SELECT',
                'execution_time' => (int)$executionTime,
                'performance' => $this->getPerformanceStatus($executionTime),
                'connection' => config('database.default'),
                'is_slow' => $executionTime > 20,
                'explain_plan' => json_encode($explainResults),
                'recommendation' => $recommendation,
            ]);

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $executionTime = 0;
        }

        return view('debug.sandbox', [
            'defaultSql' => $sql,
            'results' => $results,
            'explainResults' => $explainResults,
            'executionTime' => $executionTime ?? 0,
            'recommendation' => $recommendation,
            'error' => $error,
        ]);
    }

    /**
     * N+1 Query Detector & Alert Studio
     */
    public function nPlusOneDemo()
    {
        DB::enableQueryLog();

        // 1. Without Eager Loading (N+1 Problem)
        $n1Products = Product::all();
        $queriesN1Count = count(DB::getQueryLog());

        // 2. Optimized Query
        DB::flushQueryLog();
        $optimizedProducts = Product::where('price', '>', 50)->get();
        $queriesOptimizedCount = count(DB::getQueryLog());

        return view('debug.n1_detector', compact(
            'n1Products',
            'optimizedProducts',
            'queriesN1Count',
            'queriesOptimizedCount'
        ));
    }

    /**
     * Query Execution Benchmark Studio
     */
    public function benchmarkIndex()
    {
        return view('debug.benchmark');
    }

    public function benchmarkRun(Request $request)
    {
        $iterations = (int)$request->input('iterations', 25);
        $iterations = min(max($iterations, 5), 100);

        $sql = "SELECT * FROM products WHERE price > 50 ORDER BY id DESC";

        $times = [];
        $totalStartTime = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $t1 = microtime(true);
            DB::select($sql);
            $times[] = (microtime(true) - $t1) * 1000;
        }

        $totalDuration = round((microtime(true) - $totalStartTime) * 1000, 2);
        $minTime = round(min($times), 2);
        $maxTime = round(max($times), 2);
        $avgTime = round(array_sum($times) / count($times), 2);

        $score = 'Grade A (Excellent)';
        if ($avgTime > 15) $score = 'Grade B (Good)';
        if ($avgTime > 50) $score = 'Grade C (Needs Optimization)';
        if ($avgTime > 100) $score = 'Grade D (Slow Query)';

        return view('debug.benchmark', compact(
            'iterations',
            'sql',
            'totalDuration',
            'minTime',
            'maxTime',
            'avgTime',
            'score'
        ));
    }

    // Dashboard to show all methods & analytics
    public function dashboard()
    {
        return view('debug.dashboard', [
            'totalProducts' => Product::count(),
            'totalQueries' => QueryHistory::count(),
            'selectQueries' => QueryHistory::where('query_type', 'SELECT')->count(),
            'insertQueries' => QueryHistory::where('query_type', 'INSERT')->count(),
            'updateQueries' => QueryHistory::where('query_type', 'UPDATE')->count(),
            'deleteQueries' => QueryHistory::where('query_type', 'DELETE')->count(),
            'fastQueries' => QueryHistory::where('performance', 'Fast')->count(),
            'mediumQueries' => QueryHistory::where('performance', 'Medium')->count(),
            'slowQueries' => QueryHistory::where('performance', 'Slow')->orWhere('is_slow', true)->count(),
        ]);
    }

    // History Page
    public function history(Request $request)
    {
        $query = QueryHistory::latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('method', 'like', '%' . $request->search . '%')
                    ->orWhere('sql_query', 'like', '%' . $request->search . '%')
                    ->orWhere('query_type', 'like', '%' . $request->search . '%')
                    ->orWhere('execution_time', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->performance) {
            $query->where('performance', $request->performance);
        }

        return view('debug.history', [
            'histories' => $query->paginate(10),
            'totalQueries' => QueryHistory::count(),
            'selectQueries' => QueryHistory::where('query_type', 'SELECT')->count(),
            'insertQueries' => QueryHistory::where('query_type', 'INSERT')->count(),
            'updateQueries' => QueryHistory::where('query_type', 'UPDATE')->count(),
            'deleteQueries' => QueryHistory::where('query_type', 'DELETE')->count(),
            'fastQueries' => QueryHistory::where('performance', 'Fast')->count(),
            'mediumQueries' => QueryHistory::where('performance', 'Medium')->count(),
            'slowQueries' => QueryHistory::where('performance', 'Slow')->orWhere('is_slow', true)->count(),
        ]);
    }

    public function destroy(QueryHistory $history)
    {
        $history->delete();
        return back()->with('success', 'Deleted Successfully');
    }

    public function exportCSV()
    {
        $fileName = 'query_history.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Method', 'Query', 'Type', 'Execution Time (ms)', 'Performance', 'Slow Flag', 'Recommendation', 'Connection', 'Created At']);

            foreach (QueryHistory::latest()->get() as $history) {
                fputcsv($file, [
                    $history->id,
                    $history->method,
                    $history->sql_query,
                    $history->query_type,
                    $history->execution_time,
                    $history->performance,
                    $history->is_slow ? 'Yes' : 'No',
                    $history->recommendation ?? 'N/A',
                    $history->connection,
                    $history->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $histories = QueryHistory::latest()->get();
        return view('exports.query_pdf', compact('histories'));
    }

    public function jsonApi()
    {
        return response()->json([
            'status' => 'success',
            'data' => QueryHistory::latest()->limit(50)->get()
        ]);
    }

    public function clear()
    {
        QueryHistory::truncate();
        return back()->with('success', 'History Cleared');
    }

    // Helper methods
    private function getSqlWithBindings($query)
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }

    private function formatQuery($query)
    {
        $sql = $query->sql;
        $bindings = $query->bindings;

        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return [
            'sql' => $sql,
            'time' => $query->time . 'ms',
            'connection' => $query->connectionName,
        ];
    }

    private function formatRawQuery($query)
    {
        if (!$query) return 'No query executed';

        $sql = $query['query'] ?? '';
        $bindings = $query['bindings'] ?? [];

        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }

    private function getPerformanceStatus($time)
    {
        if ($time <= 20) return 'Fast';
        if ($time <= 80) return 'Medium';
        return 'Slow';
    }

    public function saveQueryHistory($method, $queries)
    {
        foreach ($queries as $query) {
            $sql = $this->formatRawQuery($query);
            $type = strtoupper(strtok(trim($sql), " "));
            $time = $query['time'];
            $performance = $this->getPerformanceStatus($time);

            QueryHistory::firstOrCreate(
                ['method' => $method, 'sql_query' => $sql],
                [
                    'query_type' => $type,
                    'execution_time' => $time,
                    'performance' => $performance,
                    'is_slow' => $time > 20,
                    'connection' => $query['connection'] ?? config('database.default'),
                ]
            );
        }
    }
}
