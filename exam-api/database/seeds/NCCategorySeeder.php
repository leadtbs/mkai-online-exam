<?php

use Illuminate\Database\Seeder;
use App\NCCategory;

class NCCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Fundamentals of Care', 'type' => 'E'],
            ['name' => 'Mechanisms of the Mind and the Body', 'type' => 'E'],
            ['name' => 'Communication Skills', 'type' => 'E'],
            ['name' => 'Physical Care', 'type' => 'E'],
            ['name' => 'Technical Terms of Care', 'type' => 'J'],
            ['name' => 'Communications of Care', 'type' => 'J'],
            ['name' => 'Documents of Care', 'type' => 'J'],
        ];

        NCCategory::insert($data);
    }
}
