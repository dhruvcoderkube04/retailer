<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'ticket_id', 'subject', 'ref_image','category','description', 'priority', 'status','resolved_at'
    ];
}
