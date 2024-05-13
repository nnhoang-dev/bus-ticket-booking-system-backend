<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\NhaXe;

class Route extends Model
{
    use HasFactory;
    protected $table = 'routes';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'start_address',
        'end_address',
        'time',
        'price',
        'status'
    ];

    public function start_address()
    {
        return $this->belongsTo(BusStation::class, 'start_address');
    }

    public function end_address()
    {
        return $this->belongsTo(BusStation::class, 'end_address');
    }

    // public function chuyen_xe()
    // {
    //     return $this->hasMany(ChuyenXe::class);
    // }
}
