<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends model
{
    protected $table = 'user';

    protected $fillable  = ['*'];

    public $timestamps = false;
}
