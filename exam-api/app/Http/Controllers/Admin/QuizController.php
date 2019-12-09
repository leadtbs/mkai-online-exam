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
use File;
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

    public function updateQuestion(Request $request){
        $decoded = json_decode($request->data, true);
        
        $question = Question::find($decoded['id']);

        if($request->hasFile('image')){
            $fileextension = $request->image->getClientOriginalExtension();
            $filename = (sha1(time().$request->image->getClientOriginalName())).'.'.$fileextension;
            $request->image->move(public_path('img/question'), $filename);
            $image_path = public_path().'/img/question/'.$question->picture;
            File::delete($image_path);
            $question->picture = $filename;
        }

        if($request->hasFile('audio')){
            $fileextension = $request->audio->getClientOriginalExtension();
            $filename = (sha1(time().$request->audio->getClientOriginalName())).'.'.$fileextension;
            $request->audio->move(public_path('audio'), $filename);
            $audio_path = public_path().'/audio\/'.$question->audio;
            File::delete($audio_path);
            $question->audio = $filename;
        }

        $question->save();

        foreach($decoded['choice_set'] as $d){
            $choice_set = Choice_Set::find($d['id']);
            $choice_set->description = $d['description'];
            foreach($d['choices'] as $key => $c){
                $choices = Choices::find($c['id']);
                if($d['correct'] === $key){
                    $choices->correct = 1;
                }else{
                    $choices->correct = 0;
                }
                $choices->save();
            }
            $choice_set->save();
        }

        if($request->choice_id){
            for($x = 0; $x < count($request->choice_id); $x++){
                for($y = 0; $y < count($request->choice_id[$x]); $y++){
                    $id = $request->choice_id[$x][$y];
                    $choice = Choices::find($id);
                    $choices = $request->choices;
                    $fileextension = $choices[$x][$y]->getClientOriginalExtension();
                    $filename = (sha1(time().$choices[$x][$y]->getClientOriginalName())).'.'.$fileextension;
                    $choices[$x][$y]->move(public_path('img/choices'), $filename);
                    $choice_path = public_path().'/img/choices/'.$choice->choices;
                    File::delete($choice_path);
                    $choice->choices = $filename;
                    $choice->save();
                }
            }
        }else{
            foreach($decoded['choice_set'] as $d){
                foreach($d['choices'] as $c){
                    $choice = Choices::find($c['id']);
                    $choice->choices = $c['choices'];
                    $choice->save();
                }
            }
        }
    }

    public function deleteQuestion($id){
        $question = Question::findOrFail($id);
        $question->delete();
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

    public function getQuestionsAndChoices($question_id){
        $question = Question::with('choice_set.choices')->find($question_id);

        foreach($question->choice_set as $cs){
            $x = 0;
            foreach($cs->choices as $c){
                $correct = Choices::where('id', $c->id)->pluck('correct');
                $c->choice_url = '';
                if($correct[0] === 1){
                    $cs->correct = $x;
                    $x++;
                    break;
                }else{
                    $cs->correct = '';
                }
                $x++;
            }
        }
        return $question;
    }
}
