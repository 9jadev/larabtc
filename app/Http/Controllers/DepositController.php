<?php

namespace App\Http\Controllers;

use App\Http\Requests\Deposits\CreateDepositRequest;
use App\Models\Deposit;
use App\Notifications\DepositNotification;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deposit = Deposit::whereNotNull('deposit.id');
        $deposit = $deposit->where("customer_id", auth()->user()->id)->latest()->paginate(request()->input("page_number"));

        return response()->json([
            "message" => "Fetched successfully",
            "status" => "success",
            "deposit" => $deposit,
        ]);

    }

    public function indexDeposit()
    {
        $deposit = Deposit::whereNotNull('deposit.id');
        $deposit = $deposit->latest()->paginate(request()->input("page_number"));

        return response()->json([
            "message" => "Fetched successfully",
            "status" => "success",
            "deposit" => $deposit,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateDepositRequest $request)
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
        $data = array_merge($data, [
            "customer_id" => auth()->user()->id,
        ]);
        $deposit = Deposit::create($data);
        return response()->json([
            "message" => "Proceed to make transer.",
            "status" => "success",
            "deposit" => $deposit,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function show(Deposit $deposit)
    {
        return response()->json([
            "message" => "Proceed to make transer.",
            "status" => "success",
            "deposit" => $deposit,
        ], 200);
    }

    public function toggleStatus(Deposit $deposit)
    {
        if (!$deposit) {
            return response()->json([
                "message" => "Deposit does'nt exist.",
                "status" => "error",
            ], 400);
        }

        if ($deposit->status != 0) {
            return response()->json([
                "message" => "Deposit already completed.",
                "status" => "error",
            ], 400);
        }

        $deposit->update(["status" => 1]);
        $deposit->customer->deposit($deposit->amount, ["description" => "amount"]);

        $deposit->customer->notify(new DepositNotification($deposit));

        return response()->json([
            "message" => "Deposit successful.",
            "status" => "success",
            "deposit" => $deposit,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function edit(Deposit $deposit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deposit $deposit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $deposit)
    {
        //
    }
}
