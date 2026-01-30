<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected  = [
        'name',
        'email',
        'subject',
        'content',
        'phone',
        'status', // read, unread
        'is_archived'
    ];
}
