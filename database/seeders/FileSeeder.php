<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;
use App\Models\File;

use Config;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 1; $i <= 16; $i++){

            $filePath = Storage::disk('public')->get('Test Audio Files/Test_' . $i . '.mp3');

            $fileExtension = pathinfo(public_path('Test Audio Files/Test_' . $i . '.mp3'), PATHINFO_EXTENSION);

            $originalFileName = pathinfo(public_path('Test Audio Files/Test_' . $i . '.mp3'), PATHINFO_FILENAME);

            $fullFileName = $originalFileName . '.' . $fileExtension;
            $fullFilePath = 'file/' . $originalFileName . '/' . $fullFileName;

            $filePath = Storage::disk('public')->put($fullFilePath, $filePath);

            $filePath = \Config::get('app.url') . Config::get('app.storage_path') . $fullFilePath;

            //Create file
            File::create([
                'name' => 'test_file_' . $i,
                'path' => $filePath,
                'type' => 'mp3'
            ]);
        }


    }
}
