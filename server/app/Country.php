<?php
namespace App;


use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = ['name', 'currency_name', 'lang'];

    public $timestamps = false;

    public function prices()
    {
        return $this->hasMany('App\Price');
    }
}