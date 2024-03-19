<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\File;
use Validator, Config;
use Illuminate\Support\Facades\App;

class FileController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        // $this->middleware('IsAdmin');
    }

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

    public function edit(Request $request){
        $validator = Validator::make($request->all(), [
            'id'    => 'required|exists:files,id',
            'name'  => 'required',
            'file'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }


        $file = File::find($request->id);

        $fileExtension = $request->file('file')->extension();

        $originalFileName = $request->file('file')->getClientOriginalName();

        $fullFileName = $originalFileName . $fileExtension;

        $filePath = $request->file('file')->storeAs('public/file/' . $request->name, $fullFileName);
        $filePath = substr($filePath, 7);
        $filePath = \Config::get('app.url') . Config::get('app.storage_path') . $filePath;


        $file->fill([
            'name'  => $request->name,
            'path'  => $filePath
        ]);

        $file->save();

        return response()->json(['status' => 'success', 'data' => $file], 200);
    }

    public function paginate($returnTotal)
    {
        //
        $files = File::orderBy('id')->paginate($returnTotal);

        
        return response()->json(['status' => 'success', 'data' => $files], 200);
    }
    public function search($returnTotal, Request $request)
    {   

        $keyword = $request->search;

        $query = File::orderBy('id');

        $query->where('name', 'like', '%' . $keyword . '%');
        // ->orWhere('docket_num', 'like', '%' . $keyword . '%')
        // ->orWhere('docket_num', 'like', '%' . $keyword . '%')
        // ->orWhere('first_name', 'like', '%' . $keyword . '%');

        $files = $query->paginate($returnTotal);

         return response()->json(['status' => 'success', 'data' => $files], 200);
    }
}
