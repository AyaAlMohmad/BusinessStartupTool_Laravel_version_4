<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('link')->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_global')->default(false);
            $table->timestamps();
        });

        // إنشاء جدول وسيط للعلاقة بين المستخدمين والموارد
        Schema::create('resource_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['resource_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_user');
        Schema::dropIfExists('resources');
    }
};
