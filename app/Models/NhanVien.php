<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    use HasFactory;
    protected $table = 'nhan_vien';
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
        'role',
        'status'
    ];
}