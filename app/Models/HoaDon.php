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
        'khuyen_mai_id',
        'phone_number',
        'email',
        'first_name',
        'last_name',
        'status',
        'discount',
        'price',
        'quantity',
        'total_price',
    ];
}