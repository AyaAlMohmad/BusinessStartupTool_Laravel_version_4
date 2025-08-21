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
        Schema::create('testing_your_idea', function (Blueprint $table) {
            $table->id();


            
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unsignedBigInteger('business_id')->nullable();
            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->onDelete('cascade');
            $table->json('your_idea')->nullable();

            // Desirability
            $table->boolean('solves_problem')->nullable();
            $table->json('problem_statement')->nullable();
            $table->boolean('existing_solutions_used')->nullable();
            $table->json('current_solutions_details')->nullable();
            $table->json('switch_reason')->nullable();
            $table->json('desirability_notes')->nullable();

            // Feasibility
            $table->json('required_skills')->nullable();
            $table->json('qualifications_permits')->nullable();
            $table->json('feasibility_notes')->nullable();

            // Viability
            $table->json('payment_possible')->nullable();
            $table->json('profitability')->nullable();
            $table->json('finances_details')->nullable();
            $table->json('viability_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('testing_your_idea');
    }
};
