<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'test';
    public $timestamps = false;

    public static function findOne(int $id): Model|Collection|Builder|array|null
    {
        return self::query()->find($id)->getModel();
    }

}