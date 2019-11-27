<?php

namespace App\Http\Controllers\Admin;

//use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Set;
use App\Section;
use App\Question;
use App\Choice_Set;
use App\Choices;
//use ImageOptimizer;

class QuizController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api']);
    }

    public function setName($id){
        $set = Set::find($id);

        return $set->name;
    }

    public function tabName($id){
        $section = Section::find($id);

        return $section->name;
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

    public function submitQuestionForm(Request $request){
        $decoded = json_decode($request->data, true);

        $question = new Question;

        $fileextension = $request->image->getClientOriginalExtension();
        $filename = (sha1(time().$request->image->getClientOriginalName())).'.'.$fileextension;

        $request->image->move(public_path('img/question'), $filename);

        //$img = Image::make($request->image);
        //$img->resize(500, 500)->save(public_path('img/question/'.$filename));

        $question->picture = $filename;

        if($request->hasFile('audio')){
            $fileextension = $request->audio->getClientOriginalExtension();
            $encryption =
            $filename = (sha1(time().$request->audio->getClientOriginalName())).'.'.$fileextension;
            $request->audio->move(public_path('audio'), $filename);
            $question->audio = $filename;
        }

        $question->set_id = $decoded['set_id'];
        $question->section_id = $decoded['section_id'];
        $question->choice_type = $decoded['choice_type'];
        $question->save();

        foreach($decoded['forms'] as $key => $d){
            $x = $key;
            $choice_set = new Choice_Set;
            $choice_set->question()->associate($question);
            $choice_set->description = $d['description'];
            $choice_set->save();

            $y = 0;
            if($decoded['choice_type'] == 1){
                $c = $request->choices;
                for($y = 0; $y < 4; $y++){
                    if($c[$x][$y] != null){
                        $choice = new Choices;
                        $choice->choice_set()->associate($choice_set);
                        $fileextension = $c[$x][$y]->getClientOriginalExtension();
                        $filename = (sha1(time().$c[$x][$y]->getClientOriginalName())).'.'.$fileextension;
                        $c[$x][$y]->move(public_path('img/choices'), $filename);
                        $choice->choices = $filename;
                    }
                    else{
                        $choice = new Choices;
                        $choice->choices = null;
                    }
                    $choice->correct = ($y == $d['correct']) ? 1 : 0;
                    $choice->save();
                }
            }
            else{
                foreach($d['choices'] as $c){
                    $choice = new Choices;
                    $choice->choice_set()->associate($choice_set);

                    $choice->choices = $c['choice'];
                    $choice->correct = ($y == $d['correct']) ? 1 : 0;
                    $choice->save();
                    $y++;
                }
            }
        }
    }

    public function getTabs(){
        $section = Section::all();

        return $section;
    }

    public function getTabQuestions($id, $tab){
        $question = Question::where('set_id', $id)
        ->when($tab != 0 || $tab == null, function ($query) use($tab){
            $query->where('section_id', $tab);
        })->get();

        foreach($question as $q){
            $q->choice_count = $q->choice_set->count();
        }

        return $question;
    }
}
