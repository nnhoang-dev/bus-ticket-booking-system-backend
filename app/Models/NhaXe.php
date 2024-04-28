<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaXe extends Model
{
    use HasFactory;
    protected $table = 'nha_xe';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'address',
        'phone_number',
        'status'
    ];
}