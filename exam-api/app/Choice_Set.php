<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Choice_Set extends Model
{
    protected $table = 'choice_set';
    protected $primaryKey = 'id';
    protected $fillable = [
        'question_id', 'description'
    ];

    public function question(){
        return $this->belongsTo('App\Question', 'question_id');
    }

    public function choices(){
        return $this->hasMany('App\Choices', 'choice_set_id');
    }

    public $timestamps = false;
}
