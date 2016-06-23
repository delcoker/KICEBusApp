<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultsettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defaultsettings', function (Blueprint $table) {
			
            $table->increments('defaultsetting_id');
			$table->integer('occupant_id');
			$table->integer('bus_id');
			$table->integer('driver_id');
			$table->integer('route_id');
			$table->boolean('first_time');
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
        Schema::drop('defaultsettings');
    }
}
