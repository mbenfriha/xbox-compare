<?php
/**
 * Created by PhpStorm.
 * User: patchouni
 * Date: 26/11/2016
 * Time: 00:49
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['value', 'euro_value'];

    public function game()
    {
        return $this->belongsTo('App\Game');
    }
}