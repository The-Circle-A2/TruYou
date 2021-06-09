<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $fillable = [
        'email',
        'master_password',
        'name',
    ];

    protected $hidden = [
        'master_password'
    ];

}
