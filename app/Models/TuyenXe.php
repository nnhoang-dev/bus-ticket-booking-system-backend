<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\NhaXe;

class TuyenXe extends Model
{
    use HasFactory;
    protected $table = 'tuyen_xe';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'start_address',
        'end_address',
        'time',
        'status'
    ];

    public function start_address()
    {
        return $this->belongsTo(NhaXe::class, 'start_address');
    }

    public function end_address()
    {
        return $this->belongsTo(NhaXe::class, 'end_address');
    }

    // public function chuyen_xe()
    // {
    //     return $this->hasMany(ChuyenXe::class);
    // }
}