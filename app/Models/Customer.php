<?php

namespace App\Models;

use App\Models\Deposit;
use App\Models\Investment;
use App\Models\Withdraw;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable implements Wallet
{
    use HasApiTokens, HasFactory, Notifiable, HasWallet, SoftDeletes;

    protected $table = "customers";
    protected $guarded = [];
    protected $hidden = ['otp_code', 'password', 'deleted_at'];
    protected $appends = ['customerbalance', 'customerkyc', 'funding', 'investments', 'withdraw'];

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

    public function getFundingAttribute()
    {
        $depos = Deposit::where("customer_id", $this->id)->where("status", '1');
        $amount = $depos->sum('amount');
        $count = $depos->count();
        return [
            "amount" => $amount,
            "count" => $count,
        ];
    }

    public function getInvestmentsAttribute()
    {
        $depos = Investment::where("customer_id", $this->id);
        $amount = $depos->sum('cost');
        $count = $depos->count();
        return [
            "amount" => $amount,
            "count" => $count,
        ];
    }

    public function getWithdrawAttribute()
    {
        $depos = Withdraw::where("customer_id", $this->id)->where("status", '1');
        $amount = $depos->sum('amount');
        $count = $depos->count();
        return [
            "amount" => $amount,
            "count" => $count,
        ];
    }

    public function depositlog()
    {
        return $this->hasMany(Deposit::class, 'customer_id', 'id');
    }

    public function investmentslog()
    {
        return $this->hasMany(Investment::class, 'customer_id', 'id');
    }

    public function withdrawslog()
    {
        return $this->hasMany(Withdraw::class, 'customer_id', 'id');
    }
}
