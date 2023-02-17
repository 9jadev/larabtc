<?php

namespace App\Listeners;

use App\Events\InvestmentEvent;
use App\Models\Customer;
use App\Models\Investment;
use App\Notifications\InvestmentCompletedNotification;

class InvestmentEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\InvestmentEvent  $event
     * @return void
     */
    public function handle(InvestmentEvent $event)
    {
        $invest = Investment::where("id", $event->investment)->first();
        $customer = Customer::where("id", $invest->customer_id)->first();
        $invest->update([
            "is_completed" => "1",
        ]);
        $invest->save();
        $invest->refresh();
        $amount = $invest->profit + $invest->cost + ($invest->bouns == null ? 0 : $invest->bouns);
        logs()->info(" Amount $amount");
        $customer->deposit($amount);
        $customer->notify(new InvestmentCompletedNotification($invest, $amount));
        logs()->info("sojscml " . $event->investment . " ");
    }
}
