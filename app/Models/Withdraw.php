<?php

namespace App\Models;

use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;
    protected $table = "withdraw";
    protected $guarded = [];
    protected $with = ["paymenttype"];

    public function paymenttype()
    {
        return $this->hasOne(PaymentType::class, "id", "payment_type");
    }
}
