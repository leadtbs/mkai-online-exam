<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $table = 'set';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'time', 'password'
    ];

    protected $hidden = ['password'];

    /*public function question(){
        return $this->hasMany('App\Question', 'set_id');
    }*/

    public $timestamps = false;
}
