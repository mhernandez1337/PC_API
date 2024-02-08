<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class argument extends Model
{
    use HasFactory;

    protected $table = 'arguments';

    protected $fillable = [
        'title',
        'docket_num',
        'date_time',
        'summary',
        'issues',
        'location',
        'url_key'
    ];
}
