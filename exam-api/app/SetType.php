<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SetType extends Model
{
    protected $table = 'set_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'code'
    ];
    
    public $timestamps = false;
}
