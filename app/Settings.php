<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'setting';

    public $timestamps = false;

    protected $fillable = ['*'];
}
