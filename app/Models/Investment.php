<?php

namespace App\Models;

use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\InvestmentEvent;

class Investment extends Model
{
    use HasFactory;
    protected $table = "investments";
    protected $guarded = [];
    protected $with = ["plan"];
    protected $appends = ["realtime", "timeing"];

    public function plan()
    {
        return $this->hasOne(Plan::class, "id", "plan_id");
    }
    public function getRealtimeAttribute()
    {

        $startedat_seconds = Carbon::parse($this->started_at)->addDays($this->plan->duration)->getTimestamp();
        $now = time();
        $amount = (($now) / $startedat_seconds) * ($this->profit);
        event(new InvestmentEvent($this->id));

        if ($amount > $this->profit && $this->is_completed) {
            $this->update(["is_completed" => '1']);
            $this->refresh();
            event(new InvestmentEvent($this->id));
        }
        return $amount > $this->profit ? $this->profit : $amount;
    }
    public function getTimeingAttribute()
    {
        $startedat_seconds = Carbon::parse($this->started_at)->addDays($this->plan->duration)->getTimestamp();
        if ($startedat_seconds - time() < 0) {
            return "Completed";
        }
        $now = time();
        $dd = gmdate('d:H:i:s', $startedat_seconds - $now);
        return $dd;
    }
}
