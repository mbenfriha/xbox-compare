<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Gold extends Model
{
    protected $table = 'games_gold';
    protected $fillable = [];

    public $timestamps = false;

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

}