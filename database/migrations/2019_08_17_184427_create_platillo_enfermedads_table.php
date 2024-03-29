<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatilloEnfermedadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platillo_enfermedads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('enfermedad_id');
            $table->unsignedBigInteger('platillo_id');
            $table->foreign('enfermedad_id')->references('id')->on('clasificacions');
            $table->foreign('platillo_id')->references('id')->on('platillos');
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
        Schema::dropIfExists('platillo_enfermedads');
    }
}
