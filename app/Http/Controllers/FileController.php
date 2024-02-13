<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Validator;
use Illuminate\Support\Facades\App;
use Config;

class FileController extends Controller
{
    public function index()
    {
        $files = File::orderBY('id')->get();

        return response()->json(['status' => 'success', 'data' => $files], 200);
    }

    public function single($id){
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

        $file = File::find($id);

        return response()->json(['status' => 'success', 'data' => $file], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'file'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $fileExtension = $request->file('file')->extension();

        $originalFileName = $request->file('file')->getClientOriginalName();

        $fullFileName = $originalFileName . $fileExtension;

        $filePath = $request->file('file')->storeAs('public/file/' . $request->name, $fullFileName);
        $filePath = substr($filePath, 7);
        $filePath = \Config::get('app.url') . Config::get('app.storage_path') . $filePath;

        $file = File::create([
            'name' => $request->name,
            'path' => $filePath,
            'type' => 'mp3'
        ]);

        return response()->json(['status' => 'success', 'data' => $file], 200);
    }
}
