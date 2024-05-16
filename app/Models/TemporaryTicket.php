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
        "trip_id",
        "customer_id",
        'status',
        'seat',
    ];
}
