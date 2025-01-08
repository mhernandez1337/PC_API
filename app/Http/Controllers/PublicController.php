<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class PublicController extends Controller
{
    public function __construct(){
        // $this->middleware('apikey');
    }
    /**
     * Get all recording types with their associated relationships
     * 
     * @return \Illuminate\Http\Response
     */
    public function recordings(Request $request, $eventType, $returnTotal)
    {
        echo gethostbyname($_SERVER['SERVER_NAME']);

        $events = Event::orderBy('date', 'DESC')->where('type', '=', $eventType)->with('recording')->with('recordingContent')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $events], 200);
    }

    /**
     * Get all synopsis types with their associated relationships
     * 
     * @return \Illuminate\Http\Response
     */
    public function synopsis($eventType, $returnTotal)
    {
        $events = Event::orderBy('date', 'DESC')->where('type', '=', $eventType)->with('dockets')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $events], 200);
    }

    /**
     * Get individual event with associated relationship.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $synopses = Event::orderBy('date', 'DESC')->where('type', '=', 'recording')->orWhere('type', '=', 'coa_recording')->orWhere('type', '=', 'public_hearing')->with('recordingContent')->with('recording')->get();

        return response()->json(['status' => 'success', 'data' => $synopses], 200);
    }

    /**
     * Search for a specified resource from storage.
     *
     * @param  \App\Models\Recording  $recording
     * @return \Illuminate\Http\Response
     */
    public function searchRecordings($returnTotal, Request $request)
    {   

        $keyword = $request->search;

        $query = Event::orderBy('id')->where('type', '=', $request->eventType);

        if($request->searchType == 'title'){
            $query->where('title', 'like', '%' . $keyword . '%');
        }elseif($request->searchType == 'docket_num'){
            $query->whereHas('recording', function($query) use ($keyword){
                $query->where('docket_num', '=', $keyword);
            });
        }elseif($request->searchType == 'date'){
            $query->where('date', '=', $keyword);
        }

        $events = $query->with('recordingContent')->with('recording')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $events], 200);
    }

    /**
     * Search for a specified resource from storage.
     *
     * @param  \App\Models\Recording  $recording
     * @return \Illuminate\Http\Response
     */
    public function searchSynopsis($returnTotal, Request $request)
    {   

        $keyword = $request->search;

        $query = Event::orderBy('id')->where('type', '=', $request->eventType);

        if($request->searchType == 'title'){
            $query->where('title', 'like', '%' . $keyword . '%');
        }elseif($request->searchType == 'date'){
            $query->where('date', '=', $keyword);
        }

        $events = $query->with('dockets')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $events], 200);
    }
}
