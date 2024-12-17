<?php

namespace App\Http\Controllers;

use App\Models\Recording;
use App\Models\RecordingContent;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\File;
use Validator, Config;

class RecordingController extends Controller
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
        $synopses = Event::orderBy('date', 'DESC')->where('type', '=', 'recording')->orWhere('type', '=', 'coa_recording')->orWhere('type', '=', 'public_hearing')->with('recordingContent')->with('recording')->get();

        return response()->json(['status' => 'success', 'data' => $synopses], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
        $url_key = str_replace(" ", "_", $request->title);
        $request->request->add(['url_key' => $url_key]);
        $tempContent[] = json_decode($request->content);
        // print_r($tempContent[0]);
        $validator = Validator::make($request->all(), $rules = [
            'title'             => 'required',
            'date'              => 'required',
            'type'              => 'required',
            'url_key'           => 'required|unique:events,url_key',
            'docket_num'        => '',
            'location'          => 'required',
            'time'              => 'required',
            'note'              => '',
            'appearances'       => '',
            // 'content'           => 'array',
            // 'content.*.time'    => '',
            // 'content.*.speaker' => '',
            // 'content.*.note'    => '',
            'file_name'         => 'required',
            'file'              => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $originalFileName = $request->file_name;

        $filePath = $request->file('file')->storeAs('public/file/' . $request->name, $originalFileName);
        $filePath = substr($filePath, 7);

        $filePath = \Config::get('app.url') . Config::get('app.storage_path') . $filePath;

        $file = File::create([
            'name' => $request->file_name,
            'path' => $filePath,
            'type' => 'mp3'
        ]);

        $event = Event::create([
            'title'         => $request->title,
            'date'          => $request->date,
            'type'          => $request->type,
            'url_key'       => $request->url_key
        ]);

        foreach($tempContent[0] as $ck){
            $content = RecordingContent::create([
                'time'      => $ck->time,
                'speaker'   => $ck->speaker,
                'note'      => $ck->note,
                'event_id'  => $event->id
            ]);
        }

        $recording = Recording::create([
            'title'         => $request->title,
            'docket_num'    => $request->docket_num,
            'time'          => $request->time,
            'location'      => $request->location,
            'note'          => $request->note,
            'appearances'   => $request->appearances,
            'link'          => $filePath,
            'event_id'      => $event->id
        ]);

        

        return $this->show($event->id);
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


        $event = Event::where('id', '=', $id)->with('recordingContent')->with('recording')->first();

        if($event->type == 'synopsis' || $event->type == 'coa_synopsis'){
            return response()->json(['status' => 'fail', 'data' => 'ID is not a recording event'], 400);
        }

        return response()->json(['status' => 'success', 'data' => $event], 200);
    }

    /**
     * Get all recordings with no COA or Public Hearing events.
     * Includes pagination
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllRecordings($returnTotal)
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
    public function getAllCOARecordings($returnTotal)
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
    public function getAllPublicHearings($returnTotal)
    {
        $publicHearings = Event::orderBy('date', 'DESC')->where('type', 'public_hearing')->with('recordingContent')->with('recording')->paginate($returnTotal);

        return response()->json(['status' => 'success', 'data' => $publicHearings], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recording  $recording
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tempArray[] = json_decode($request->content);

        $validator = Validator::make($request->all(), $rules = [
            'title'             => 'required',
            'date'              => 'required',
            'type'              => 'required',
            'docket_num'        => '',
            'location'          => 'required',
            'time'              => 'required',
            'note'              => '',
            'appearances'       => '',
            'link'              => ''
            // 'content'           => 'array',
            // 'content.*.time'    => '',
            // 'content.*.speaker' => '',
            // 'content.*.note'    => '',
            // 'content.*.active'  => '',
            // 'content.*.id'  => '',
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'fail', 'data' => $validator->messages()], 400);
        }

        $event = Event::where('id', '=', $id)->first();


        $event->fill([
            'title'         => $request->title,
            'date'          => $request->date,
            'type'          => $request->type
        ]);

        $event->save();

        $recording = Recording::where('event_id', '=', $event->id)->first();

        if($request->file_change == 'true'){
            $originalFileName = $request->file_name;

            $filePath = $request->file('file')->storeAs('public/file/' . $request->name, $originalFileName);
            $filePath = substr($filePath, 7);

            $filePath = \Config::get('app.url') . Config::get('app.storage_path') . $filePath;
        }else{
            $filePath = $request->link;
        }

        if($recording){
            $recording->fill([
                'title'         => $request->title,
                'docket_num'    => $request->docket_num,
                'time'          => $request->time,
                'location'      => $request->location,
                'note'          => $request->note,
                'appearances'   => $request->appearances,
                'link'          => $filePath
            ]);
            $recording->save();
        }

        

        // $file = File::where('id', '=', ([
        //     'name' => $request->file_name,
        //     'path' => $filePath,
        //     'type' => 'mp3'
        // ]);
        
        if(count($tempArray[0]) > 0){
            foreach($tempArray[0] as $ck){
                if($ck->id){
                    $content = RecordingContent::where('id', '=', $ck->id)->first();
                    $content->fill([
                        'time'      => $ck->time,
                        'speaker'   => $ck->speaker,
                        'note'      => $ck->note,
                        'active'    => $ck->active,
                        'event_id'  => $event->id
                    ]);
                    $content->save();
                }else{
                    $content = RecordingContent::create([
                        'time'      => $ck->time,
                        'speaker'   => $ck->speaker,
                        'note'      => $ck->note,
                        'active'    => $ck->active,
                        'event_id'  => $event->id
                    ]);
                }
            }
        }
        

        return $this->show($event->id);
    }

    /**
     * Deactivate the specified resource from storage.
     *
     * @param  \App\Models\Recording  $recording
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Recording $recording)
    {
        //
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
}
