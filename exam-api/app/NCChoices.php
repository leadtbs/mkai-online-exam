<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NCChoices extends Model
{
    protected $table = 'nc_choices';
    protected $primaryKey = 'id';
    protected $fillable = [
        'choice_set_id', 'choices', 'correct'
    ];

    protected $hidden = ['correct'];

    public function choice_set(){
        return $this->belongsTo('App\NCChoiceSet', 'choice_set_id');
    }
    
    public $timestamps = false;
}
