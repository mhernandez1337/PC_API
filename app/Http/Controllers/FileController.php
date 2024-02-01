<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Validator;

class FileController extends Controller
{
    public function index()
    {
        $files = File::orderBY('id')->get();

        return response()->json(['status' => 'success', 'data' => $files], 200);
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

        $file = File::create([
            'name' => $request->name,
            'path' => $filePath,
            'type' => 'mp3'
        ]);

        return response()->json(['status' => 'success', 'data' => $file], 200);
    }
}
