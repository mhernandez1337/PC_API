<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docket extends Model
{
    use HasFactory;

    protected $table = 'dockets';

    protected $fillable = [
        'title',
        'docket_num',
        'time',
        'summary',
        'panel',
        'location',
        'event_id',
        'active'
    ];

    public function dockets(){
        return $this->belongsTo('App/Models/Event', 'id', 'event_id');
    }
}
