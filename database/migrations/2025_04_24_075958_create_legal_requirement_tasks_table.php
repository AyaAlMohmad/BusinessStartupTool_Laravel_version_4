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
        Schema::create('legal_requirement_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_structure_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('status')->nullable();
            $table->string('deadline')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_requirement_tasks');
    }
};
