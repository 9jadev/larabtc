<?php

namespace App\Http\Controllers;

use App\Http\Requests\Investments\CreateInvestmentRequest;
use App\Models\Investment;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $investment = Investment::whereNotNull("investments.id");
        $investment->where("customer_id", auth()->user()->id);
        request()->input("status") ? $investment->where("status", request()->input("status")) : $investment;
        $investment = $investment->latest()->paginate(request()->input("page_number"));

        return response()->json([
            "message" => "Fetched support",
            "status" => "success",
            "investments" => $investment,
        ], 200);
    }

    public function indexList()
    {
        $investment = Investment::whereNotNull("investments.id");
        request()->input("status") ? $investment->where("status", request()->input("status")) : $investment;
        $investment = $investment->latest()->paginate(request()->input("page_number"));

        return response()->json([
            "message" => "Fetched support",
            "status" => "success",
            "investments" => $investment,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateInvestmentRequest $request)
    {
        $data = $request->validated();
        return $this->store($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($data)
    {
        if (auth()->user()->let_invest == false) {
            return response()->json([
                "message" => "You can't invest at this time contact support",
                "status" => "error",
            ], 400);
        }
        if (auth()->user()->customerbalance < $data["cost"]) {
            return response()->json([
                "message" => "Account balance is low, fund account to continue.",
                "status" => "error",
            ], 400);
        }
        $plan = Plan::where("id", $data["plan_id"])->first();
        $data = array_merge([
            "customer_id" => auth()->user()->id,
            "plan_id" => $data["plan_id"],
            "cost" => $data["cost"],
            "bouns" => $plan->gift_bouns,
            "profit" => (($data["cost"] * ($plan->percentage / 100)) + $plan->gift_bouns),
            "started_at" => $this->startdateformat($plan),
            "ended_at" => $this->enddateformat($plan),
        ], $data);
        auth()->user()->withdraw($data["cost"]);
        $investment = Investment::create($data);
        return response()->json([
            "message" => "Investment Create Successfully",
            "status" => "success",
            "investment" => $investment,
        ]);
    }

    private function enddateformat(Plan $plan)
    {
        $current = Carbon::now();
        $enddate = $current->addDays($plan->duration);
        return $enddate->toDateTimeString();
    }

    private function startdateformat(Plan $plan)
    {
        $current = Carbon::now();

        return $current->toDateTimeString();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function show(Investment $investment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function edit(Investment $investment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Investment $investment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Investment  $investment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Investment $investment)
    {
        //
    }
}
