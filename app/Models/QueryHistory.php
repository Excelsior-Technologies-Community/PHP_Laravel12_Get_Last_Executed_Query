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
        'connection',
    ];
}