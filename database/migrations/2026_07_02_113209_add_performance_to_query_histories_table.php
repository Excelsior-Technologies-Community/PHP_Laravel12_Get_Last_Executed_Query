<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('query_histories', function (Blueprint $table) {

            $table->string('performance')
                ->default('Fast')
                ->after('execution_time');

        });
    }

    public function down(): void
    {
        Schema::table('query_histories', function (Blueprint $table) {

            $table->dropColumn('performance');

        });
    }
};