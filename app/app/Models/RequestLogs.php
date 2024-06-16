<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLogs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'host',
        'type',
        'url',
        'params',
        'request_body',
        'requested_at',
        'response_body',
        'responded_at',
        'duration',
        'status'
    ];
    
    public $timestamps = false;
}
