<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Docket;
use Validator;
class SynopsesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Get all synopsis and coa synopis' events with dockets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $synopses = Event::orderBy('date', 'DESC')->where('type', 'synopsis')->orWhere('type','coa_synopsis')->with('dockets')->get();

        return response()->json(['status' => 'success', 'data' => $synopses], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recording  $recording
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rules = [
            'id'    => 'required|exists:events,id',
        ];

        $data = [
            'id'    => $id,
        ];

        $validator = Validator::make($data, $rules);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $event = Event::where('id', '=', $id)->with('dockets')->first();

        if($event->type == 'recording' || $event->type == 'coa_recording' || $event->type == 'public_hearing'){
            return response()->json(['status' => 'fail', 'data' => 'ID is not a synopsis event'], 400);
        }

        return response()->json(['status' => 'success', 'data' => $event], 200);
    }
    

    /**
     * Get all synopsis with no COA events.
     * Includes pagination
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllSynopses($returnTotal)
    {
        $synopses = Event::orderBy('date', 'DESC')->where('type', 'synopsis')->with('dockets')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $synopses], 200);
    }

    /**
     * Get all COA synopsis events.
     * Includes pagination
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllCOASynopses($returnTotal)
    {
        $synopses = Event::orderBy('date', 'DESC')->where('type','coa_synopsis')->with('dockets')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $synopses], 200);
    }

    /**
     * Show the form for creating a new synopses  or coa synopses.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // echo $request->docket[0]['docket_title'];
        $url_key = str_replace(" ", "_", $request->title);
        $request->request->add(['url_key' => $url_key]);

        $validator = Validator::make($request->all(), $rules = [
            'title'                 => 'required',
            'date'                  => 'required',
            'type'                  => 'required',
            'url_key'               => 'required|unique:events,url_key',
            'docket'                => 'array',
            'docket.*.docket_title' => '',
            'docket.*.docket_num'   => '',
            'docket.*.location'     => '',
            'docket.*.time'         => '',
            'docket.*.panel'        => '',
            'docket.*.summary'      => ''
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }


        $event = Event::create([
            'title'         => $request->title,
            'date'          => $request->date,
            'type'          => $request->type,
            'url_key'       => $url_key
        ]);

        foreach($request->dockets as $dk){
            $docket = Docket::create([
                'title'         => $dk['title'],
                'docket_num'    => $dk['docket_num'],
                'location'      => $dk['location'],
                'time'          => $dk['time'],
                'panel'         => $dk['panel'],
                'summary'       => $dk['summary'],
                'event_id'      => $event->id
            ]);
        }

        return $this->show($event->id);
    }

    /**
     * Update synopses  or coa synopses.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), $rules = [
            'title'                 => 'required',
            'date'                  => 'required',
            'type'                  => 'required',
            'docket'                => 'array',
            'docket.*.title'        => '',
            'docket.*.docket_num'   => '',
            'docket.*.location'     => '',
            'docket.*.time'         => '',
            'docket.*.panel'        => '',
            'docket.*.summary'      => '',
            'docket.*.active'       => ''
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $event = Event::where('id', '=', $request->id)->first();
        $event->fill([
            'title'         => $request->title,
            'date'          => $request->date,
            'type'          => $request->type,
        ]);

        $event->save();

        if(count($request->dockets) > 0){
            foreach($request->dockets as $dk){
                if($dk['id']){
                    $docket = Docket::where('id', '=', $dk['id'])->first();
                    $docket->fill([
                        'title'         => $dk['title'],
                        'docket_num'    => $dk['docket_num'],
                        'location'      => $dk['location'],
                        'time'          => $dk['time'],
                        'panel'         => $dk['panel'],
                        'summary'       => $dk['summary'],
                        'active'        => $dk['active'],
                        'event_id'      => $event->id
                    ]);
                    $docket->save();
                }else{
                    $docket = Docket::create([
                        'title'         => $dk['title'],
                        'docket_num'    => $dk['docket_num'],
                        'location'      => $dk['location'],
                        'time'          => $dk['time'],
                        'panel'         => $dk['panel'],
                        'summary'       => $dk['summary'],
                        'event_id'      => $event->id
                    ]);
                }
            }
        }

        return $this->show($event->id);
    }

    /**
     * Search for a specified resource from storage.
     *
     * @param  \App\Models\Recording  $recording
     * @return \Illuminate\Http\Response
     */
    public function search($returnTotal, Request $request)
    {   

        $keyword = $request->search;

        $query = Event::orderBy('id')->where('type', '=', $request->type);

        if($request->searchType == 'title'){
            $query->where('title', 'like', '%' . $keyword . '%');
        }elseif($request->searchType == 'date'){
            $query->where('date', '=', $keyword);
        }

        $events = $query->with('dockets')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $events], 200);
    }
}
