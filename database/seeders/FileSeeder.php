<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\File;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create file
        File::create([
        'name' => 'test_file',
        'path' => 'some\path\test_file.mp3',
        'type' => 'mp3'
        ]);

    }
}
