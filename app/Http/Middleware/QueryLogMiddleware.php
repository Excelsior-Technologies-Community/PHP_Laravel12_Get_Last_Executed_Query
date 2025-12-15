<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QueryLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only log queries in local environment or when debug is enabled
        if (app()->environment('local') || config('app.debug')) {
            DB::enableQueryLog();
            
            DB::listen(function ($query) {
                $sql = $query->sql;
                $bindings = $query->bindings;
                
                // Replace bindings in SQL
                foreach ($bindings as $binding) {
                    $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
                    $sql = preg_replace('/\?/', $value, $sql, 1);
                }
                
                Log::channel('queries')->info('Query Executed', [
                    'sql' => $sql,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                    'url' => request()->fullUrl(),
                ]);
            });
        }
        
        return $next($request);
    }
}