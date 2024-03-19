<?php

namespace Database\Seeders;
use App\Models\Argument;
use DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
class ArgumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locationArray = ['Carson City, NV', 'Las Vegas, NV'];
        for($i = 1; $i <= 15; $i++){
            $location = '';
            if($i % 2 == 0){
                $location = $locationArray[0];
            }else{
                $location = $locationArray[1];
            }
            DB::table('arguments')->insert([
                'title'         => 'Title ' . $i,
                'docket_num'    => 'ADKT-100' . $i,
                'date_time'    =>  Carbon::now()->addDays($i)->format('Y-m-d H:i:s'),
                'location'      => $location,
                'summary'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Velit ut tortor pretium viverra suspendisse potenti nullam. Massa enim nec dui nunc mattis enim ut tellus. Egestas sed sed risus pretium. Sed risus ultricies tristique nulla aliquet enim tortor.',
                'issues'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Id aliquet lectus proin nibh nisl condimentum id venenatis.',
                'url_key'       => 'some/url/key/' . $i
            ]);
        }
    }
}
