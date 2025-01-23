<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsCampaign extends Model
{
    protected $table = 'campaigns';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'title'
    ];
}