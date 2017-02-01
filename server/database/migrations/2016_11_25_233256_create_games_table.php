<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function($table)
        {
            $table->engine = 'InnoDB';

            $table->string('id')->unique();
            $table->string('slug');
            $table->string('name');
            $table->string('type');
            $table->longText('description');
            $table->string('video');
            $table->string('studio');
            $table->string('size');
            $table->string('gamescore');
            $table->string('addon_id');
            $table->boolean('discount');

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
        Schema::dropIfExists('games');
    }
}
