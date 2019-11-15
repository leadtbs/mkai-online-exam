<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataTestController extends Controller
{
    public function __invoke(Request $request){
        $user = $request->user();

        return response()->json([
            'username' => $user->username,
            'name' => $user->name,
        ]);
    }
}
