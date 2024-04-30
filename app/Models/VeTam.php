<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeTam extends Model
{
    use HasFactory;
    protected $table = 've_tam';
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
