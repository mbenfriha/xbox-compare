<?php
/**
 * Created by PhpStorm.
 * User: patchouni
 * Date: 26/11/2016
 * Time: 00:49
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['id', 'slug', 'name', 'thumb', 'background'];

    public function prices()
    {
        return $this->hasMany('App\Price');
    }
}