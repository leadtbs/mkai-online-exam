<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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
use PDF;

class GuestController extends Controller
{
    public function confirmPassword(Request $request){
        $id = $request->id;
        $set_type = $request->set_type;
        $set_type_let = ($set_type == 'nce') ? 'E' : 'J';
        $set = Set::find($id);
        if(Hash::check($request->form['password'], $set->password)){
            info($set_type);
            if($set_type == 'jlt'){
                $section = Section::with(['question' => function($query) use($id){
                    $query->with('choice_set.choices')->where('set_id', $id);
                }])->get();
                    
                $set->section = $section;
            }else{
                $question = NCQuestion::with('choice_set.choices')->where('set_id', $id)->get();

                $set->question = $question;
            }

            $set->set_type = $set_type;
            $set->stud_name = $request->form['name'];
            $set->stud_sensei = $request->form['sensei'];
            return $set;
        }else{
            return 'wrong';
        }
    }

    public function startExam($id){
        $set = Set::find($id);

        $section = Section::with('question', 'question.choice_set.choices')
                ->whereHas('question', function($query) use($id){
                    $query->where('set_id', $id);
                })->get();
    
            $set->section = $section;
            return $set;

        return $set;
    }

    public function submitExam(Request $request){
        $scores = [];
        $result = [];
        $total_score = 0; $total_total = 0;

        foreach($request->section as $s){
            $total = 0;
            $score = 0;
            foreach($s['question'] as $q){
                foreach($q['choice_set'] as $c){
                    $total++;
                    $index = $c['picked'];
                    if($index !== null){
                        $id = $c['choices'][$index]['id'];
                        $picked = Choices::find($id);
                        if($picked->correct){
                            $score++;
                        }
                    }
                }
            }

            $total_score += $score;
            $total_total += $total;

            data_set($result, 'section', $s['name']);
            data_set($result, 'score', $score . '/' . $total . ' ('. round(($score / $total) * 100) . '%)');
            data_set($result, 'passing', ceil($total * .80));
            data_set($result, 'status', ((($score / $total) * 100) >= 80) ? 'Passed' : 'Failed');
            array_push($scores, $result);
        }

        data_set($result, 'section', 'TOTAL');
        data_set($result, 'score', $total_score . '/' . $total_total . ' (' . round(($total_score / $total_total) * 100) . '%)');
        data_set($result, 'passing', ceil($total_total * .80));
        data_set($result, 'status', ((($total_score / $total_total) * 100) >= 80) ? 'Passed' : 'Failed');
        array_push($scores, $result);   

        $html = '';
        
        $html .= '<h1>Set Name: '.$request['name'].'</h1>';
        $html .= '<table border="1"><tbody>';

        return [
            'set_name' => $request['name'],
            'stud_name' =>$request['stud_name'],
            'stud_sensei' =>$request['stud_sensei'],
            'scores' => $scores
        ];
    }

    public function submitNCExam(Request $request){
        $set_type = $request->set_type;
        $scores = [];
        $result = [];
        $total_score = 0; $total_total = 0;

        $category = NCCategory::
                    when($set_type == 'nce', function($query){
                        $query->where('type', 'E');
                    })
                    ->when($set_type == 'ncj', function($query){
                        $query->where('type', 'J');
                    })->get();

        foreach($category as $cat){
            $total = 0;
            $score = 0;

            foreach($request->exam['question'] as $q){
                if($q['category_id'] == $cat->id){
                    foreach($q['choice_set'] as $c){
                        $total++;
                        $index = $c['picked'];
                        if($index !== null){
                            $id = $c['choices'][$index]['id'];
                            $picked = NCChoices::find($id);
                            if($picked->correct){
                                $score++;
                            }
                        }
                    }
                }
            }

            $total_score += $score;
            $total_total += $total;

            data_set($result, 'category', $cat['name']);
            data_set($result, 'score', $score . '/' . $total . ' ('. round(($score / $total) * 100) . '%)');
            data_set($result, 'passing', ceil($total * .80));
            data_set($result, 'status', ((($score / $total) * 100) >= 80) ? 'Passed' : 'Failed');
            array_push($scores, $result);
        }

            data_set($result, 'category', 'TOTAL');
            data_set($result, 'score', $total_score . '/' . $total_total . ' (' . round(($total_score / $total_total) * 100) . '%)');
            data_set($result, 'passing', ceil($total_total * .80));
            data_set($result, 'status', ((($total_score / $total_total) * 100) >= 80) ? 'Passed' : 'Failed');
            array_push($scores, $result);  

            $html = '';
            
            $html .= '<h1>Set Name: '.$request['exam']['name'].'</h1>';
            $html .= '<table border="1"><tbody>';

            return [
                'set_name' => $request['exam']['name'],
                'stud_name' =>$request['exam']['stud_name'],
                'stud_sensei' =>$request['exam']['stud_sensei'],
                'scores' => $scores
            ];
    }
    
    public function getSetAll(Request $request){
        $set_type = $request->set_type;
        $temp = Set::with('question.choice_set.choices')->where('set_type_id', 1)->get();
        if($set_type != 1){
            $tempNC = Set::with('nc_question.choice_set.choices')->where('set_type_id', $set_type)->get();
        }
        $set = Set::where('set_type_id', $set_type)->get();
        
        if($set_type == 1){
            for($x = 0; $x < count($set); $x++){
                $set[$x]['question_count'] = $temp[$x]['question']->count();
                $choice_count = 0;
                if(!($temp[$x]['question']->isEmpty())){
                    foreach($temp[$x]['question'] as $q){
                        foreach($q->choice_set as $cs){
                            $choice_count++;
                        }
                    }
                }
                $set[$x]['choice_count'] = $choice_count;
            }
        }else{
            for($x = 0; $x < count($set); $x++){
                $set[$x]['question_count'] = $tempNC[$x]['nc_question']->count();
                $choice_count = 0;
                if(!($tempNC[$x]['nc_question']->isEmpty())){
                    foreach($tempNC[$x]['nc_question'] as $q){
                        foreach($q->choice_set as $cs){
                            $choice_count++;
                        }
                    }
                }
                $set[$x]['choice_count'] = $choice_count;
            }
        }

        return $set;
    }
}
