<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'section';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name'
    ];

    public function question(){
        return $this->hasMany('App\Question', 'section_id');
    }

    public $timestamps = false;
}
