<?php

namespace App\Listeners;

use App\Events\InvestmentEvent;
use App\Models\Investment;

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
        $investment = Investment::where("id", $event->investment->id)->first();
        $investment->update([
            "is_completed" => "1",
        ]);
        logs()->info("sojscml " . $event->investment->id);
    }
}
