<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\SetType;
use App\Set;

class SetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Japanese Language Test', 'code' => 'JLT'],
            ['name' => 'Nursing Care (English)', 'code' => 'NCE'],
            ['name' => 'Nursing Care (Japanese)', 'code' => 'NCJ'],
        ];

        SetType::insert($data);

        $set = Set::all();
        foreach($set as $s){
            $s->set_type_id = 1;
            $s->save();
        }
    }
}
