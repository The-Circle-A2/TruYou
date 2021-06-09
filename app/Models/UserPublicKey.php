<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPublicKey extends Model
{

    protected $fillable = [
        'public_key'
    ];

    protected $hidden = [
        'public_key',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
