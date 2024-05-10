<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'type',
        'title',
        'date',
        'active',
        'url_key'
    ];

    public function dockets() {
        return $this->hasMany('App\Models\Docket', 'event_id', 'id')->orderBy('time')->where('active', '=', 1);
    }

    public function recording() {
        return $this->hasOne('App\Models\Recording', 'event_id', 'id')->orderBy('id');
    }

    public function recordingContent() {
        return $this->hasMany('App\Models\RecordingContent', 'event_id', 'id')->orderBy('time')->where('active', '=', 1);
    }
}
