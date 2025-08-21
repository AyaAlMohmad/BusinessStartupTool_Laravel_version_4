<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversionRatesTable extends Migration
{
    public function up()
    {
        Schema::create('conversion_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('business_id')->nullable();



            
            // Foreign Keys
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->onDelete('cascade');
            $table->float('target_revenue');
            $table->float('unit_price');
            $table->float('interactions_needed');
            // $table->float('engagement_needed');
            // $table->float('reach_needed');


$table->float('reach_to_interaction_percentage'); // 👈 بدل engagement_needed و reach_needed

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversion_rates');
    }
}
