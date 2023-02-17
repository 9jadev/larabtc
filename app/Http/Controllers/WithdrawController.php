<?php

namespace App\Http\Controllers;

use App\Models\Withdraw;
use App\Notifications\WithdrawalNotification;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $withdrawals = Withdraw::where("customer_id", auth()->user()->id);
        request("status") == null ? $withdrawals : $withdrawals->where("status", request("status"));
        $withdrawals = $withdrawals->latest()->paginate(request()->input("page_number"));
        return response()->json([
            "message" => "Fetched successfully",
            "status" => "success",
            "withdrawals" => $withdrawals,
            "Stax" => request("status"),
        ], 200);
    }

    public function indexList()
    {
        $withdrawals = Withdraw::latest();
        request("status") == null ? $withdrawals : $withdrawals->where("status", request("status"));
        $withdrawals = $withdrawals->latest()->paginate(request()->input("page_number"));
        return response()->json([
            "message" => "Fetched successfully",
            "status" => "success",
            "withdrawals" => $withdrawals,
            "Stax" => request("status"),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $amount = request()->input("amount");
        $payment_type = request()->input("payment_type");

        if (!$amount) {
            return response()->json([
                "message" => "Amount is request",
                "status" => "error",
            ], 400);
        }
        if (auth()->user()->customerbalance <= $amount) {
            return response()->json([
                "message" => "Account balance is low, fund account to continue.",
                "status" => "error",
            ], 400);
        }
        if (auth()->user()->let_withdraw === null) {
            return response()->json([
                "message" => "You can't withdraw at this time contact support.",
                "status" => "error",
            ], 400);
        }
        $withdraw = Withdraw::create([
            "amount" => $amount,
            "customer_id" => auth()->user()->id,
            "payment_type" => $payment_type,
            "status" => auth()->user()->let_withdraw == '0' ? 0 : 1,
        ]);

        if (auth()->user()->let_withdraw == '1') {
            auth()->user()->withdraw($amount);
            $customer = auth()->user();
            $customer->notify(new WithdrawalNotification($withdraw));
            return response()->json([
                "message" => "Withdrawal successful.",
                "status" => "success",
                "withdraw" => $withdraw,
            ], 200);
        }

        return response()->json([
            "message" => "Withdrawal successful contact support for confirmation.",
            "status" => "success",
            "withdraw" => $withdraw,
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function toggleWithdraw(Withdraw $withdraw)
    {
        if (!$withdraw) {
            return response()->json([
                "message" => "withdraw does'nt exist.",
                "status" => "error",
            ], 400);
        }

        if ($withdraw->status != 0) {
            return response()->json([
                "message" => "Withdraw already completed.",
                "status" => "error",
            ], 400);
        }

        if ($withdraw->customer->customerbalance <= $withdraw->amount) {
            return response()->json([
                "message" => "Account balance is low, fund account to continue.",
                "status" => "error",
            ], 400);
        }

        $withdraw->update(["status" => 1]);

        $withdraw->customer->withdraw($withdraw->amount);

        $withdraw->customer->notify(new WithdrawalNotification($withdraw));

        return response()->json([
            "message" => "Withdrawal successful.",
            "status" => "success",
            "withdraw" => $withdraw,
        ], 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function edit(Withdraw $withdraw)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Withdraw $withdraw)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function destroy(Withdraw $withdraw)
    {
        //
    }
}
