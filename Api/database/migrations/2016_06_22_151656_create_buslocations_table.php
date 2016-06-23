<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuslocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buslocations', function (Blueprint $table) {
            $table->increments('buslocation_id');
			$table->string('name')->default('');
			$table->string('longitude')->default('');
			$table->string('latitude')->default('');
			$table->integer('route_id');
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
        Schema::drop('buslocations');
    }
}
