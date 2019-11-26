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
use PDF;

class GuestController extends Controller
{
    public function confirmPassword(Request $request){
        $id = $request->id;
        $set = Set::find($id);
        if(Hash::check($request->password, $set->password)){
            $section = Section::with('question', 'question.choice_set.choices')
                ->whereHas('question', function($query) use($id){
                    $query->where('set_id', $id);
                })->get();
    
            $set->section = $section;
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
            data_set($result, 'score', $score . '/' . $total);
            data_set($result, 'percent', round(($score / $total) * 100) . '%');
            array_push($scores, $result);
        }

        data_set($result, 'section', 'TOTAL');
        data_set($result, 'score', $total_score . '/' . $total_total);
        data_set($result, 'percent', round(($total_score / $total_total) * 100) . '%');
        array_push($scores, $result);

        $html = '';
        
        $html .= '<h1>Set Name: '.$request['name'].'</h1>';
        $html .= '<table border="1"><tbody>';

        return [
            'set_name' => $request['name'],
            'scores' => $scores
        ];
    }
    
    public function getSetAll(){
        $set = Set::with('question.choice_set.choices')->get();

        foreach($set as $s){
            $s->question_count = $s->question->count();
            if(!($s->question->isEmpty())){
                foreach($s->question as $q){
                    foreach($q->choice_set as $cs){
                        $s->choice_count++;
                    }
                }
            }else{
                $s->choice_count = 0;
            }
        }

        return $set;
    }
}
