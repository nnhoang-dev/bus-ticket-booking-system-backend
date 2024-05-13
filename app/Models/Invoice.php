<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'customer_id',
        'transaction_id',
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
