<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('query_histories', function (Blueprint $table) {
            $table->boolean('is_slow')->default(false)->after('performance');
            $table->boolean('is_n_plus_one')->default(false)->after('is_slow');
            $table->text('explain_plan')->nullable()->after('is_n_plus_one');
            $table->text('recommendation')->nullable()->after('explain_plan');
        });
    }

    public function down(): void
    {
        Schema::table('query_histories', function (Blueprint $table) {
            $table->dropColumn(['is_slow', 'is_n_plus_one', 'explain_plan', 'recommendation']);
        });
    }
};
