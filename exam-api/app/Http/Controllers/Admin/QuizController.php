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
use App\NCCategory;
use App\NCChoices;
use App\NCChoiceSet;
use App\NCQuestion;
use App\SetType;
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

    public function tabName($id, Request $request){
        $id = $id-1;
        if($request->set_type == 'jlt'){
            $tabs = Section::find($id);
        }else if($request->set_type == 'nce'){
            $tabs = NCCategory::where('id', $id)->where('type', 'E')->first();
        }else if($request->set_type == 'ncj'){
            $tabs = NCCategory::where('id', $id)->where('type', 'J')->first();
        }

        return $tabs->name;
    }

    public function saveSet(Request $request){
        $set_type = $request->set_type;
        $time = explode(':', $request->time);
        $time = $time[0] * 3600 + $time[1] * 60;

        return Set::create([
            'name' => $request['name'],
            'time' => $time,
            'password' => bcrypt($request['password']),
            'set_type_id' => $set_type
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

        $isJLT = $request->set_type == 'jlt' ? true : false;
        
        $question = ($isJLT) ? new Question : new NCQuestion;

        $fileextension = $request->image->getClientOriginalExtension();
        $filename = (sha1(time().$request->image->getClientOriginalName())).'.'.$fileextension;

        if($isJLT){
            $request->image->move(public_path('img/question'), $filename);
        }else{
            $request->image->move(public_path('img/ncquestion'), $filename);
        }

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
        ($isJLT) ? $question->section_id = $decoded['section_id']-1 : $question->category_id = $decoded['section_id']-1;
        $question->choice_type = $decoded['choice_type'];
        $question->save();

        foreach($decoded['forms'] as $key => $d){
            $x = $key;
            $choice_set = ($isJLT) ? new Choice_Set : new NCChoiceSet;
            $choice_set->question()->associate($question);
            $choice_set->description = $d['description'];
            $choice_set->save();

            $y = 0;
            if($decoded['choice_type'] == 1){
                $c = $request->choices;
                for($y = 0; $y < 4; $y++){
                    if($c[$x][$y] != null){
                        $choice = ($isJLT) ? new Choices : new NCChoices;
                        $choice->choice_set()->associate($choice_set);
                        $fileextension = $c[$x][$y]->getClientOriginalExtension();
                        $filename = (sha1(time().$c[$x][$y]->getClientOriginalName())).'.'.$fileextension;
                        if($isJLT){
                            $c[$x][$y]->move(public_path('img/choices'), $filename);
                        }else{
                            $c[$x][$y]->move(public_path('img/ncchoices'), $filename);
                        }
                        $choice->choices = $filename;
                    }
                    else{
                        $choice = ($isJLT) ? new Choices : new NCChoices;
                        $choice->choices = null;
                    }
                    $choice->correct = ($y === $d['correct']) ? 1 : 0;
                    $choice->save();
                }
            }
            else{
                foreach($d['choices'] as $c){
                    $choice = ($isJLT) ? new Choices : new NCChoices;
                    $choice->choice_set()->associate($choice_set);

                    $choice->choices = $c['choice'];
                    $choice->correct = ($y === $d['correct']) ? 1 : 0;
                    $choice->save();
                    $y++;
                }
            }
        }
    }

    public function updateQuestion(Request $request){
        $isJLT = ($request->set_type == 'jlt') ? true : false;
        $decoded = json_decode($request->data, true);
        
        $question = ($isJLT) ? Question::find($decoded['id']) : NCQuestion::find($decoded['id']);

        if($request->hasFile('image')){
            $q = ($isJLT) ? 'question' : 'ncquestion';
            $fileextension = $request->image->getClientOriginalExtension();
            $filename = (sha1(time().$request->image->getClientOriginalName())).'.'.$fileextension;
            $request->image->move(public_path('img/'.$q), $filename);
            $image_path = public_path().'/img\/'.$q.'/'.$question->picture;
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
            
            $choice_set = ($isJLT) ? Choice_Set::find($d['id']) : NCChoiceSet::find($d['id']);
            $choice_set->description = $d['description'];
            foreach($d['choices'] as $key => $c){
                $choices = ($isJLT) ? Choices::find($c['id']) : NCChoices::find($c['id']);
                info($choices);
                info($key);
                if($d['correct'] === $key){
                    info('correct');
                    $choices->correct = 1;
                }else{
                    $choices->correct = 0;
                }
                $choices->save();
            }
            $choice_set->save();
        }

        if($request->choice_id){
            $c = ($isJLT) ? 'choices' : 'ncchoices';
            for($x = 0; $x < count($request->choice_id); $x++){
                for($y = 0; $y < count($request->choice_id[$x]); $y++){
                    $id = $request->choice_id[$x][$y];
                    $choice = ($isJLT) ? Choices::find($id) : NCChoices::find($id) ;
                    $choices = $request->choices;
                    $fileextension = $choices[$x][$y]->getClientOriginalExtension();
                    $filename = (sha1(time().$choices[$x][$y]->getClientOriginalName())).'.'.$fileextension;
                    $choices[$x][$y]->move(public_path('img/'+$c), $filename);
                    $choice_path = public_path().'/img\/'+$c+'/'.$choice->choices;
                    File::delete($choice_path);
                    $choice->choices = $filename;
                    $choice->save();
                }
            }
        }else{
            foreach($decoded['choice_set'] as $d){
                foreach($d['choices'] as $c){
                    $choice = ($isJLT) ? Choices::find($c['id']) : NCChoices::find($c['id']);
                    $choice->choices = $c['choices'];
                    $choice->save();
                }
            }
        }
    }

    public function deleteQuestion($id, Request $request){
        if($request->set_type == 'jlt'){
            $question = Question::findOrFail($id);
        }else{
            $question = NCQuestion::findOrFail($id);
        }
        $question->delete();
    }

    public function getTabs(Request $request){
        if($request->set_type == 1){
            $tabs = Section::all();
        }else if($request->set_type == 2){
            $tabs = NCCategory::where('type', 'E')->get();
        }else if($request->set_type == 3){
            $tabs = NCCategory::where('type', 'J')->get();
        }

        return $tabs;
    }

    public function getTabQuestions($id, $tab, Request $request){
        $set_type = $request->set_type;
        if($set_type == 'jlt'){
            $question = Question::where('set_id', $id)
            ->when($tab != 0 || $tab == null, function ($query) use($tab){
                $query->where('section_id', $tab);
            })->get();
        }else{
            $question = NCQuestion::where('set_id', $id)
            ->when($set_type == 'nce', function($query){
                $query->whereHas('category', function($q){
                    $q->where('type', 'E');
                });
            })
            ->when($set_type == 'ncj', function($query){
                $query->whereHas('category', function($q){
                    $q->where('type', 'J');
                });
            })
            ->when($tab != 0 || $tab == null, function ($query) use($tab){
                $query->where('category_id', $tab);
            })->get();
        }

        foreach($question as $q){
            $q->choice_count = $q->choice_set->count();
        }

        return $question;
    }

    public function getQuestionsAndChoices($question_id, Request $request){
        $isJLT = ($request->set_type == 'jlt') ? true : false;
        $question = ($isJLT) ? Question::with('choice_set.choices')->find($question_id)
                            : NCQuestion::with('choice_set.choices')->find($question_id);

        foreach($question->choice_set as $cs){
            $x = 0;
            foreach($cs->choices as $c){
                $correct = ($isJLT) ? Choices::where('id', $c->id)->pluck('correct') : NCChoices::where('id', $c->id)->pluck('correct');
                $c->choice_url = '';
                if($correct[0] == 1){
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
