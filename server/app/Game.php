<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'slug', 'name'];

    public function prices()
    {
        return $this->hasMany('App\Price');
    }

    public function gold()
    {
        return $this->hasMany('App\Gold');
    }

    public function alert()
    {
        return $this->hasMany('App\Alert');
    }
}