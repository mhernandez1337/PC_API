<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator, Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('IsAdmin');
    }

    public function index() {
        $users = User::orderBy('last_name')->orderBy('first_name')->get();

        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    public function createUser(Request $request){
        $rules = [
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|unique:users,email',
            'password'      => 'required|min:8|confirmed'
        ];

        $data = [
            'first_name'                => $request->first_name,
            'last_name'                 => $request->last_name,
            'email'                     => $request->email,
            'password'                  => $request->password,
            'password_confirmation'     => $request->password_confirmation
        ];

        $validator = Validator::make($data,$rules);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $user = User::Create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password)
        ]);

        return response()->json(['status' => 'success', 'data' => $user], 201);
    }

    public function editUser(Request $request){
        $rules = [
            'id'            => 'required|exists:users,id',
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required',
            'active'        => 'required|integer|between:0,1',
            'role'          => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_USER,
            // 'password'      => 'required'
        ];

        $data = [
            'id'            => $request->id,
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'active'        => $request->active,
            'role'          => $request->role,
            // 'password'      => $request->password
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $user = User::find($request->id);

        $user->fill([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'active'        => $request->active,
            'role'          => $request->role,
            // 'password'      => Hash::make($request->password)
        ]);

        $user->save();

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    public function updatePassword(Request $request){

        $rules = [
            'id'            => 'required|exists:users,id',
            'password'      => 'required|min:8|confirmed',
        ];

        $data = [
            'id'                    => $request->id,
            'password'              => $request->password,
            'password_confirmation' => $request->password_confirmation
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $user = User::find($request->id);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    public function paginate($status, $returnTotal)
    {
        $users = User::orderBy('last_name')->where('active', '=', $status)->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    public function search($returnTotal, Request $request)
    {   

        $keyword = $request->search;

        $query = User::orderBy('last_name');

        if($request->searchType == 'name'){
            $query->where('last_name', 'like', '%' . $keyword . '%')
            ->orWhere('first_name', 'like', '%' . $keyword . '%');
        }else{
            $query->where('email', 'like', '%' . $keyword . '%'); 
        }

        $users = $query->paginate($returnTotal);

         return response()->json(['status' => 'success', 'data' => $users], 200);
    }
}
