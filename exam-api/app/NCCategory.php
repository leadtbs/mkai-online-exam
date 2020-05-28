<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NCCategory extends Model
{
    protected $table = 'nc_category';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'type'
    ];

    public function question(){
        return $this->hasMany('App\NCQuestion', 'category_id');
    }
    
    public $timestamps = false;
}
