<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    use HasFactory;
    protected $table = 'khach_hang';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'phone_number',
        'password',
        'email',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'address',
        'status'
    ];
}
