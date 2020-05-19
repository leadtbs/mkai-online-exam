<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NCQuestion extends Model
{
    protected $table = 'nc_question';
    protected $primaryKey = 'id';
    protected $fillable = [
        'set_id', 'category_id', 'picture'
    ];
    
    public function set(){
        return $this->hasOne('App\Set', 'id', 'set_id');
    }

    public function category(){
        return $this->hasOne('App\NCCategory', 'id', 'category_id');
    }

    public function choice_set(){
        return $this->hasMany('App\NCChoiceSet', 'question_id');
    }
    
    public $timestamps = false;
}
