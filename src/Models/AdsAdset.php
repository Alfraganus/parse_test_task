<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsAdset extends Model
{
    protected $table = 'sets';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'title'
    ];
}