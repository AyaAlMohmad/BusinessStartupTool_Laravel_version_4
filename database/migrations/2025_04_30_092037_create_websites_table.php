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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name')->nullable();
            $table->text('business_description')->nullable();
            $table->integer('colour_choice')->nullable();
            $table->integer('logo_style_choice')->nullable();
            $table->text('about_us')->nullable();
            $table->text('social_proof')->nullable();
            $table->json('contact_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
