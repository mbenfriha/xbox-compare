<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Alert extends Model
{
    protected $table = 'users_alert';
    protected $fillable = ['email', 'game_id', 'price', 'send'];

    public function game()
    {
        return $this->belongsTo('App\Game');
    }
}
