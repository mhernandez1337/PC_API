<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Argument;
use Validator;

class ArgumentController extends Controller
{
    //Pull all arguments
    public function index()
    {
        $arguments = Argument::OrderBy('id')->get();

        return response()->json(['status' => 'success', 'data' => $arguments], 200);
    }

    //Store arguments
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title'         =>  'required',
            'docket_num'    =>  'required',
            'date_time'     =>  'required',
            'summary'       =>  'required',
            'location'      =>  'required',
            'issues'        =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $urlKey = $request->date_time . '_' . $request->location . '_' . 'attending_justices';

        $argument = Argument::create([
            'title'         =>  $request->title,
            'docket_num'    =>  $request->docket_num,
            'date_time'     =>  $request->date_time,
            'summary'       =>  $request->summary,
            'location'      =>  $request->location,
            'issues'        =>  $request->issues,
            'url_key'       =>  $urlKey
        ]);

        return response()->json(['status' => 'success', 'data' => $argument], 200);
    }

    function single($id) {
        $rules = [
            'id'    =>  'required'
        ];

        $data = [
            'id'    => $id
        ];

        $validator = Validator::make($data,$rules);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $argument = Argument::find($id);

        return response()->json(['status' => 'success', 'data' => $argument], 200);
    }
}
