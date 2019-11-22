<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'question';
    protected $primaryKey = 'id';
    protected $fillable = [
        'picture', 'set_id', 'section_id', 'audio', 'choice_type'
    ];

    public function set(){
        return $this->hasOne('App\Set', 'id', 'set_id');
    }

    public function section(){
        return $this->hasOne('App\Section', 'id', 'section_id');
    }

    public function choice_set(){
        return $this->hasMany('App\Choice_Set', 'question_id');
    }

    public $timestamps = false;
}
