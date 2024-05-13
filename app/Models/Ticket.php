<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        "id",
        'ticket_id',
        "trip_id",
        "invoice_id",
        "customer_id",
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'route_name',
        'date',
        'start_time',
        'end_time',
        'start_address',
        'end_address',
        'seat',
        'price',
        'license',
    ];

    // public function chuyen_xe()
    // {
    //     return $this->belongsTo(ChuyenXe::class, 'chuyen_xe_id');
    // }

    // public function hoa_don()
    // {
    //     return $this->belongsTo(HoaDon::class, 'hoa_don_id');
    // }

    // public function khach_hang()
    // {
    //     return $this->belongsTo(KhachHang::class, 'khach_hang_id');
    // }
}
