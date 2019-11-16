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

    public function setName($id){
        $set = Set::find($id);

        return $set->name;
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

    public function updateSet(Request $request, $id){
        $set = Set::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191|unique:set,name,'.$set->id,
            'time' => 'required|string',
            'password' => 'sometimes|required|min:3',
        ]);

        $time = explode(':', $request->time);
        $time = $time[0] * 3600 + $time[1] * 60;
        $request->merge(['time' => $time]);

        if(!empty($request->password)){
            $request->merge(['password' => bcrypt($request['password'])]);
        }

        $set->update($request->all());
    }

    public function deleteSet(Request $request, $id){
        $set = Set::findOrFail($id);
        $set->delete();
    }
}
