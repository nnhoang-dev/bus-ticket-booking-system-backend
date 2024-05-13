<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $table = 'trips';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        "id",
        "route_id",
        "bus_id",
        "driver_id",
        'price',
        "seat",
        "date",
        "start_time",
        "end_time",
        "status",
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }
}
