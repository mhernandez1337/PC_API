<?php

namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\RecordingContent;
use Illuminate\Http\Request;
use Validator;

class SiteController extends Controller
{
    /**
     * Get all recordings with no COA or Public Hearing events.
     * Includes pagination
     *
     * @return \Illuminate\Http\Response
     */
    public function getRecordings($returnTotal)
    {
        $recordings = Event::orderBy('date', 'DESC')->where('type', 'recording')->with('recordingContent')->with('recording')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $recordings], 200);
    }

    /**
     * Get all COA recordings with no recordings or Public Hearing events.
     * Includes pagination
     *
     * @return \Illuminate\Http\Response
     */
    public function getCOARecordings($returnTotal)
    {
        $coaRecordings = Event::orderBy('date', 'DESC')->where('type', 'coa_recording')->with('recordingContent')->with('recording')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $coaRecordings], 200);
    }

    /**
     * Get all Public Hearings with no COA or Recordings events.
     * Includes pagination
     *
     * @return \Illuminate\Http\Response
     */
    public function getPublicHearings($returnTotal)
    {
        $publicHearings = Event::orderBy('date', 'DESC')->where('type', 'public_hearing')->with('recordingContent')->with('recording')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $publicHearings], 200);
    }

    /**
     * Get all synopsis with no COA events.
     * Includes pagination
     *
     * @return \Illuminate\Http\Response
     */
    public function getSynopses($returnTotal)
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
    public function getCOASynopses($returnTotal)
    {
        $synopses = Event::orderBy('date', 'DESC')->Where('type','coa_synopsis')->with('dockets')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $synopses], 200);
    }

    /**
     * Search for a specified resource from storage.
     *
     * @param  \App\Models\Recording  $recording
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request, $returnTotal)
    {   

        $keyword = $request->search;
        $searchType = $request->search_type;
        $eventType = $request->event_type;

        if($eventType == 'recording' || $eventType == 'coa_recording' || $eventType == 'public_hearing'){
            if($searchType == 'title'){
                $query = Event::orderBy('date', 'DESC')->where('type', '=', $eventType)->where($searchType, 'like', '%' . $keyword . '%')->with('recording')->with('recordingContent')->paginate($returnTotal);
                return response()->json(['status' => 'success', 'data' => $query], 200);
            }
            else if ($searchType == 'speaker'){
                $query = Event::orderBy('date', 'DESC')->whereHas('recordingContent', function($q) use ($searchType, $keyword){
                    $q->where($searchType, 'like', '%' . $keyword . '%');
                })->with('recordingContent')->with('recording')->paginate($returnTotal);
                return response()->json(['status' => 'success', 'data' => $query], 200);
            }else if($searchType == 'docket_num'){
                $query = Event::orderBy('date', 'DESC')->whereHas('recording', function($q) use ($searchType, $keyword){
                    $q->where($searchType, 'like', '%' . $keyword . '%');
                })->with('recordingContent')->with('recording')->paginate($returnTotal);
                return response()->json(['status' => 'success', 'data' => $query], 200);
            }else if($searchType == 'date'){
                $query = Event::orderBy('date', 'DESC')->where('type', '=', $eventType)->where($searchType, '=', $keyword)->with('recording')->with('recordingContent')->paginate($returnTotal);
                return response()->json(['status' => 'success', 'data' => $query], 200);
            }
        }
        else if($eventType == 'synopsis' || $eventType == 'coa_synopsis'){
            if($searchType == 'title'){
                $query = Event::orderBy('date', 'DESC')->where('type', '=', $eventType)->where($searchType, 'like', '%' . $keyword . '%')->paginate($returnTotal);
                return response()->json(['status' => 'success', 'data' => $query], 200);
            }else if($searchType == 'date'){
                $query = Event::orderBy('date', 'DESC')->where('type', '=', $eventType)->where($searchType, '=', $keyword)->paginate($returnTotal);
                return response()->json(['status' => 'success', 'data' => $query], 200);
            }else if($searchType == 'docket_title'){
                $query = Event::orderBy('date', 'DESC')->whereHas('dockets', function($q) use ($searchType, $keyword){
                    $q->where('title', 'like', '%' . $keyword . '%');
                })->paginate($returnTotal);
                return response()->json(['status' => 'success', 'data' => $query], 200);
            }else if($searchType == 'docket_num'){
                $query = Event::orderBy('date', 'DESC')->whereHas('dockets', function($q) use ($searchType, $keyword){
                    $q->where($searchType, 'like', '%' . $keyword . '%');
                })->paginate($returnTotal);
                return response()->json(['status' => 'success', 'data' => $query], 200);
            }
        }

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
