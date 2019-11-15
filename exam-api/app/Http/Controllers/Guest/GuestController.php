<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Set;

class GuestController extends Controller
{
    public function getSetAll(){
        $set = Set::all();

        return $set;
    }
}
