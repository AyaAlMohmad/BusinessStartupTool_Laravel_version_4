<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversion_rates', function (Blueprint $table) {
            $table->boolean('progress')->default(false)->after('reach_to_interaction_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversion_rates', function (Blueprint $table) {
         $table->dropColumn('progress');
        });
    }
};
