<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recording extends Model
{
    use HasFactory;

    protected $table = 'recordings';

    protected $fillable = [
        'title',
        'docket_num',
        'link',
        'note',
        'appearances',
        'time',
        'location',
        'event_id'
    ];
}
