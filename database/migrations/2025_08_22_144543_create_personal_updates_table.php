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
        Schema::create('personal_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('increased_language_proficiency')->default(false);
            $table->boolean('increased_clarity_about_employment')->default(false);
            $table->boolean('increased_business_clarity')->default(false);
            $table->boolean('increased_confidence')->default(false);
            $table->boolean('increased_my_network')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_updates');
    }
};
