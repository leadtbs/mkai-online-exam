<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NCChoiceSet extends Model
{
    protected $table = 'nc_choice_set';
    protected $primaryKey = 'id';
    protected $fillable = [
        'question_id', 'category_id', 'picture'
    ];

    public function question(){
        return $this->belongsTo('App\NCQuestion', 'question_id');
    }

    public function choices(){
        return $this->hasMany('App\NCChoices', 'choice_set_id');
    }
    
    public $timestamps = false;
}
