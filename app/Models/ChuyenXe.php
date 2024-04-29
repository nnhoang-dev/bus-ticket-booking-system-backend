<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChuyenXe extends Model
{
    use HasFactory;
    protected $table = 'chuyen_xe';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        "id",
        "tuyen_xe_id",
        "xe_id",
        "seat",
        "date",
        "start_time",
        "end_time",
        "status"
    ];

    public function xe()
    {
        return $this->belongsTo(Xe::class, 'xe_id');
    }

    public function tuyen_xe()
    {
        return $this->belongsTo(TuyenXe::class, 'tuyen_xe_id');
    }
}