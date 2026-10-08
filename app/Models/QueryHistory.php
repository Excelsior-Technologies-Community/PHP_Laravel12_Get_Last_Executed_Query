<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QueryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'sql_query',
        'query_type',
        'execution_time',
        'performance',
        'connection',
        'is_slow',
        'is_n_plus_one',
        'explain_plan',
        'recommendation',
    ];

    protected $casts = [
        'is_slow' => 'boolean',
        'is_n_plus_one' => 'boolean',
        'execution_time' => 'integer',
    ];
}