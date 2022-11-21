<?php

namespace App\Models;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable implements Wallet
{
    use HasApiTokens, HasFactory, Notifiable, HasWallet;

    protected $table = "customers";
    protected $guarded = [];
    protected $hidden = ['otp_code', 'password'];
    protected $appends = ['customerbalance', 'customerkyc'];

    public function getCustomerbalanceAttribute()
    {
        return $this->balanceInt;
    }
    public function getCustomerkycAttribute()
    {
        if ($this->id_card && $this->image) {
            return false;
        }
        return true;
    }
}
