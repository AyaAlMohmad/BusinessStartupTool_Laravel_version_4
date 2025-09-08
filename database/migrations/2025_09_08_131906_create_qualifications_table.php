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
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('migrant_profile_id')->on('migrant_profiles')->constrained()->onDelete('cascade');
            $table->enum('level', ['primary', 'secondary', 'trade', 'bachelor', 'diploma', 'master', 'phd']);
            $table->string('details')->nullable();
            $table->string('institution')->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('qualifications');
    }
};
