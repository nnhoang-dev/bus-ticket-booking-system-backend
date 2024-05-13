<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryTicket extends Model
{
    use HasFactory;
    protected $table = 'temporary_tickets';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        "id",
        "chuyen_xe_id",
        "khach_hang_id",
        'status',
        'seat',
    ];
}
