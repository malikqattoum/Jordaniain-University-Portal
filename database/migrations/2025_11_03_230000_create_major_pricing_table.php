<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMajorPricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('major_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('major_name'); // Major name in Arabic
            $table->string('major_key')->unique(); // Key for matching (e.g., 'law', 'engineering')
            $table->decimal('hourly_rate', 8, 2); // Hourly rate in USD
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('major_key');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('major_pricing');
    }
}
