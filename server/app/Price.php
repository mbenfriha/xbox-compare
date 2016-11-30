<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'games_prices';
    protected $fillable = ['value', 'euro_value', 'country_id'];

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    public function getLowestAttribute ()
    {
        return $this->prices->min('price');
    }
}