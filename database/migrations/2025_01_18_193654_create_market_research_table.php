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
        Schema::create('market_research', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');

            $table->string('target_customer_name')->nullable();
            $table->string('age')->nullable();
            $table->string('income')->nullable();
            $table->string('employment')->nullable();
            $table->string('gender')->nullable();
            $table->string('other')->nullable();
            $table->string('education')->nullable();
            $table->json('must_have_solutions')->nullable();
            $table->json('should_have_solutions')->nullable();
            $table->json('nice_to_have_solutions')->nullable();
            $table->json('help_persona')->nullable();
            $table->json('problem')->nullable();
            $table->json('solution')->nullable();
            $table->json('nots')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_research');
    }
};
