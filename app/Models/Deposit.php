<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;
    protected $table = "deposit";
    protected $guarded = [];
    protected $with = ["paymenttype", "customer"];

    public function paymenttype()
    {
        return $this->hasOne(PaymentType::class, "id", "payment_type");
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }
}
