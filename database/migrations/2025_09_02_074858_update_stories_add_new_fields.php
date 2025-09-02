<?php

// database/migrations/2025_09_02_000001_update_stories_add_new_fields.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            // معلومات البيزنس
            $table->string('business_name')->nullable();

            $table->string('business_description')->nullable();
            $table->string('business_solution')->nullable();
            $table->string('business_impact')->nullable();
            $table->string('future_plans')->nullable();

            // تواصل
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();

            // صورة البروفايل (مسار داخل public/images)
            $table->string('profile_photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'my_story',
                'business_description',
                'business_solution',
                'business_impact',
                'future_plans',
                'email',
                'website',
                'phone',
                'profile_photo',
            ]);
        });
    }
};
