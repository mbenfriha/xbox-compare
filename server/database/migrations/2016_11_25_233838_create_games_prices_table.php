<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateGamesPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_prices', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('game_id');
            $table->unsignedInteger('country_id');
            $table->float('value');
            $table->float('euro_value');

            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games_prices');
    }
}
