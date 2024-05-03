<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordingContent extends Model
{
    use HasFactory;

    protected $table = 'recording_contents';

    protected $fillable = [
        'title',
        'time',
        'event_id',
        'speaker',
        'note',
        'active'
    ];
}
