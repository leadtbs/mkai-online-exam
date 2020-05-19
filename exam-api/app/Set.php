<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $table = 'set';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'time', 'password', 'set_type_id'
    ];

    protected $hidden = ['password'];

    public function question(){
        return $this->hasMany('App\Question', 'set_id');
    }

    public function nc_question(){
        return $this->hasMany('App\NCQuestion', 'set_id');
    }

    public function set_type(){
        return $this->hasOne('App\SetType', 'id', 'set_type_id');
    }

    public $timestamps = false;
}
