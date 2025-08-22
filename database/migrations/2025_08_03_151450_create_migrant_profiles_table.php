<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('migrant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Personal Info
            $table->string('name')->nullable();
            $table->string('birth_place')->nullable();
            $table->integer('birth_year')->nullable();
            $table->enum('status', ['Migrant', 'Refugee', 'Aboriginal', 'Other'])->nullable();
            $table->string('cultural_background')->nullable();
            $table->string('languages')->nullable();
            $table->integer('arrival_year')->nullable();
            $table->string('visa_category')->nullable();
            // $table->string('region')->nullable();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');


            // Business Info
            $table->enum('business_stage', ['idea', 'started', 'operational'])->nullable();
            $table->text('business_idea')->nullable();
            $table->boolean('has_abn')->default(false);
            $table->boolean('has_website')->default(false);
            $table->string('website_url')->nullable();
            $table->boolean('has_social_media')->default(false);
            $table->text('social_links')->nullable();

            // Employment Info
            $table->enum('employment_status', ['employed', 'unemployed', 'student', 'retired', 'other'])->nullable();
            $table->string('employment_role')->nullable();

            // Education Info
            $table->enum('is_studying', ['yes', 'no'])->nullable();
            $table->enum('education_level', ['primary', 'secondary', 'trade', 'bachelor', 'diploma', 'master', 'phd'])->nullable();
            $table->string('trade_details')->nullable();
            $table->string('bachelor_details')->nullable();
            $table->string('diploma_details')->nullable();
            $table->string('master_details')->nullable();
            $table->string('phd_details')->nullable();

            $table->timestamps();
        });

        Schema::create('employment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('migrant_profiles')->onDelete('cascade');
            $table->string('role')->nullable();
            $table->string('company')->nullable();
            $table->string('industry')->nullable();
            $table->integer('years')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employment_history');
        Schema::dropIfExists('migrant_profiles');
    }
};
