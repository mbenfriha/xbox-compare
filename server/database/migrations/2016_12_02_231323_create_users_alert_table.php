<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersAlertTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_alert', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('email');
            $table->string('price');
            $table->string('game_id');
            $table->boolean('send');

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
        Schema::dropIfExists('users_alert');
    }
}
