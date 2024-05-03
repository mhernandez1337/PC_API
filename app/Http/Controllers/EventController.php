<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Validator;

class EventController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::orderBy('date', 'DESC')->with('dockets')->with('recordingContent')->with('recording')->get();

        return response()->json(['status' => 'success', 'data' => $events], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rules = [
            'id'    => 'required|exists:events,id'
        ];

        $data = [
            'id'    => $id
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $event = Event::where('id', '=', $id)->with('dockets')->get();

        return response()->json(['status' => 'success', 'data' => $event], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Find a specified resource based on URL key.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function findByUrlKey($url_key)
    {
        $data = [
            'url_key'   => $url_key
        ];

        $rules = [
            'url_key'   => 'required|exists:events,url_key'
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 200);
        }

        $event = Event::where('url_key', '=', $url_key)->first();

        if($event->type == 'synopsis' || $event->type == 'coa_synopsis'){
            $event = Event::where('url_key', '=', $url_key)->with('dockets')->first();
        }
        else {
            $event = Event::where('url_key', '=', $url_key)->with('recordingContent')->with('recording')->first();
        }

        return response()->json(['status' => 'success', 'data' => $event], 200);
    }
}
