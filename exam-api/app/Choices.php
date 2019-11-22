<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Choices extends Model
{
    protected $table = 'choices';
    protected $primaryKey = 'id';
    protected $fillable = [
        'choice_set_id', 'choices', 'correct'
    ];

    protected $hidden = ['correct'];

    public function choice_set(){
        return $this->belongsTo('App\Choice_Set', 'choice_set_id');
    }

    public $timestamps = false;
}
