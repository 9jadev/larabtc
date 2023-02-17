<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plan = Plan::get();
        return response()->json([
            "message" => "Plan listed successfully.",
            "status" => "success",
            "plans" => $plan,
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "plan_name" => "required|string",
            "min_amount" => "required|string",
            "max_amount" => "required|string",
            "percentage" => "required|string",
            "gift_bouns" => "nullable",
            "duration" => "required|string",
        ]);
        $plan = Plan::create([
            "plan_name" => $request->plan_name,
            "min_amount" => $request->min_amount,
            "max_amount" => $request->max_amount,
            "percentage" => $request->percentage,
            "gift_bouns" => $request->gift_bouns,
            "duration" => $request->duration,
        ]);
        return response()->json([
            "message" => "created successfully.",
            "status" => "success",
            "plan" => $plan,
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            "plan_name" => "required|string",
            "min_amount" => "required|string",
            "max_amount" => "required|string",
            "percentage" => "required|string",
            "gift_bouns" => "nullable",
            "duration" => "required|string",
        ]);
        $plan->update([
            "plan_name" => $request->plan_name,
            "min_amount" => $request->min_amount,
            "max_amount" => $request->max_amount,
            "percentage" => $request->percentage,
            "gift_bouns" => $request->gift_bouns,
            "duration" => $request->duration,
        ]);

        return response()->json([
            "message" => "updated successfully.",
            "status" => "success",
            "plan" => $plan,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json([
            "message" => "deleted successfully.",
            "status" => "success",
            "data" => $plan,
        ], 200);
    }
}
