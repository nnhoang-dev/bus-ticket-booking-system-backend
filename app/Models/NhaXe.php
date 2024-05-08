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
        "city",
        'address',
        'phone_number',
        'status'
    ];

    // public function tuyen_xe()
    // {
    //     return $this->hasMany(TuyenXe::class);
    //     // return $this->hasOne(TuyenXe::class, 'id');
    // }
}
