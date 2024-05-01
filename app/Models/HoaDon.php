<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;
    protected $table = 'hoa_don';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'khach_hang_id',
        'giao_dich_id',
        'phone_number',
        'email',
        'first_name',
        'last_name',
        'discount',
        'price',
        'quantity',
        'total_price',
    ];
}
