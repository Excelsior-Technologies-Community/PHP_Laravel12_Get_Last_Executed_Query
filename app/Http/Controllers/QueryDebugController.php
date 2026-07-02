<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QueryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryDebugController extends Controller
{
    // Method 1: Using DB::getQueryLog() - Requires enabling query log
    public function method1()
    {
        // Enable query log
        DB::enableQueryLog();

        // Execute some queries
        $products = Product::where('price', '>', 100)->get();
        $count = Product::count();
        $expensiveProducts = Product::where('price', '>', 500)->orderBy('price', 'desc')->get();

        // Get query log
        $queries = DB::getQueryLog();

        $this->saveQueryHistory('Method 1', $queries);

        // Format all queries for display
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

        // Get SQL without bindings
        $rawSql = $query->toSql();

        // Get SQL with bindings replaced
        $sqlWithBindings = $this->getSqlWithBindings($query);

        // Actually execute the query
        $products = $query->get();
        $queries = DB::getQueryLog();

        $this->saveQueryHistory('Method 2', $queries);

        return view('debug.method2', [
            'products' => $products,
            'rawSql' => $rawSql,
            'sqlWithBindings' => $sqlWithBindings,
        ]);
    }

    // Method 3: Using DB::listen() for all queries
    public function method3()
    {
        DB::enableQueryLog();

        $lastQuery = null;
        $formattedLastQuery = null;

        // Listen to all queries
        DB::listen(function ($query) use (&$lastQuery, &$formattedLastQuery) {
            $lastQuery = $query;
            $formattedLastQuery = $this->formatQuery($query);

            // You can also log to file or console
            Log::info('Executed Query:', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });

        // Execute some queries
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

    // Method 4: Using a Global Scope (Middleware) to log all queries
    public function method4()
    {
        // Enable query log for demonstration
        DB::enableQueryLog();

        // This method shows how you can track queries globally
        // Execute various types of queries
        $allProducts = Product::all();
        $firstProduct = Product::first();
        $newProduct = Product::create([
            'name' => 'Tablet',
            'description' => 'Latest tablet',
            'price' => 399.99,
            'quantity' => 15,
        ]);

        // Get and format queries
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

    // Method 5: Using Raw SQL and getting last query
    public function method5()
    {
        // Enable query log for this demonstration
        DB::enableQueryLog();

        // Execute raw SQL
        DB::select('SELECT * FROM products WHERE quantity > ?', [20]);

        // Get the last query
        $queries = DB::getQueryLog();
        $this->saveQueryHistory('Method 5', $queries);
        $lastQuery = end($queries);

        // Format it nicely
        $formattedQuery = $lastQuery ? $this->formatRawQuery($lastQuery) : 'No query executed';

        return view('debug.method5', [
            'lastQuery' => $lastQuery,
            'formattedQuery' => $formattedQuery,
            'queries' => $queries,
        ]);
    }

    // Helper method to get SQL with bindings
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

    // Helper method to format query from DB::listen
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

    // Helper method to format raw query
    private function formatRawQuery($query)
    {
        if (!$query) {
            return 'No query executed';
        }

        $sql = $query['query'] ?? '';
        $bindings = $query['bindings'] ?? [];

        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }

    public function saveQueryHistory($method, $queries)
    {
        foreach ($queries as $query) {

            $sql = $this->formatRawQuery($query);

            $type = strtoupper(strtok(trim($sql), " "));

            QueryHistory::firstOrCreate(
                [
                    'method' => $method,
                    'sql_query' => $sql,
                ],
                [
                    'query_type' => $type,
                    'execution_time' => $query['time'],
                    'connection' => $query['connection'] ?? 'mysql',
                ]
            );
        }
    }

    // Dashboard to show all methods
    public function dashboard()
    {
        return view('debug.dashboard', [

            'totalProducts' => Product::count(),

            'totalQueries' => QueryHistory::count(),

            'selectQueries' => QueryHistory::where('query_type', 'SELECT')->count(),

            'insertQueries' => QueryHistory::where('query_type', 'INSERT')->count(),

            'updateQueries' => QueryHistory::where('query_type', 'UPDATE')->count(),

            'deleteQueries' => QueryHistory::where('query_type', 'DELETE')->count(),

        ]);
    }

    public function history(Request $request)
    {
        $query = QueryHistory::oldest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('method', 'like', '%' . $request->search . '%')
                    ->orWhere('sql_query', 'like', '%' . $request->search . '%')
                    ->orWhere('query_type', 'like', '%' . $request->search . '%')
                    ->orWhere('execution_time', 'like', '%' . $request->search . '%');
            });
        }

        return view('debug.history', [
            'histories' => $query->paginate(4),
            'totalQueries' => QueryHistory::count(),
            'selectQueries' => QueryHistory::where('query_type', 'SELECT')->count(),
            'insertQueries' => QueryHistory::where('query_type', 'INSERT')->count(),
            'updateQueries' => QueryHistory::where('query_type', 'UPDATE')->count(),
            'deleteQueries' => QueryHistory::where('query_type', 'DELETE')->count(),
        ]);
    }

    public function destroy(QueryHistory $history)
    {
        $history->delete();

        return back()->with('success', 'Deleted Successfully');
    }

    public function clear()
    {
        QueryHistory::truncate();

        return back()->with('success', 'History Cleared');
    }

}