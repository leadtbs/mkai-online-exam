<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Set;

class QuizController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api']);
    }

    public function saveSet(Request $request){
        $time = explode(':', $request->time);
        $time = $time[0] * 3600 + $time[1] * 60;

        return Set::create([
            'name' => $request['name'],
            'time' => $time,
            'password' => bcrypt($request['password'])
        ]);
    }
}
