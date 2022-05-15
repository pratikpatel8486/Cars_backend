<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 100);
            $table->string('modal', 50);
            $table->string('variant', 50);
            $table->string('make_year', 50);
            $table->string('reg_year', 50);
            $table->string('fuel_type', 50);
            $table->string('ownership', 50);
            $table->string('kms', 50);
            $table->string('rto', 50);
            $table->string('transmission', 50);
            $table->string('insurance', 50);
            $table->string('insurance_date', 50);
            $table->string('color', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
