<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{





    protected $table = 'occupants';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    
    protected $fillable = ['name', 'password','username'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
