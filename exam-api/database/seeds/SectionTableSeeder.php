<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Section;

class SectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Script & Vocabulary'],
            ['name' => 'Grammar & Conversation'],
            ['name' => 'Listening Comprehension'],
            ['name' => 'Reading Comprehension']
        ];

        Section::insert($data);
    }
}
