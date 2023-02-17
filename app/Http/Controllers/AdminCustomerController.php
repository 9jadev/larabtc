<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Notifications\BonusWithdrawalNotification;
use App\Notifications\BonusDepositNotification;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Customer::latest();
        $search = request()->input("search_term");
        !empty($search) ? $query->where(function ($q) use ($search) {
            $q->where('username', 'like', '%' . $search . '%')
                ->orWhere('fullname', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        }) : $query;

        $customers = $query->paginate(request()->input("page_number"));
        return response()->json([
            "message" => "Fetch successfully",
            "status" => "success",
            "customers" => $customers,
        ]);
    }

    public function disabeCustomer()
    {
        $id = request()->input("id");
        $status = request()->input("status");

        $customer = Customer::where("id", $id)->first();
        if (!$customer) {
            return response()->json([
                "message" => "No customer found.",
                "status" => "error",
            ], 400);
        }
        $customer->update(["status" => $status]);
        $customer->refresh();
        return response()->json([
            "message" => "Status shared successfully.",
            "status" => "success",
            "customer" => $customer,
        ], 200);
    }

    public function letInvest()
    {
        $id = request()->input("id");
        $status = request()->input("status");

        $customer = Customer::where("id", $id)->first();
        if (!$customer) {
            return response()->json([
                "message" => "No customer found.",
                "status" => "error",
                "customer" => $customer,
            ], 400);
        }
        $customer->update(["let_invest" => $status]);
        $customer->refresh();
        return response()->json([
            "message" => "Investment status successfully updated.",
            "status" => "success",
            "customer" => $customer,
        ], 200);
    }

    public function letWithdraw()
    {
        $id = request()->input("id");
        $status = request()->input("status");

        $customer = Customer::where("id", $id)->first();
        if (!$customer) {
            return response()->json([
                "message" => "No customer found.",
                "status" => "error",
            ], 400);
        }
        $customer->update(["let_withdraw" => $status]);
        $customer->refresh();
        return response()->json([
            "message" => "withdrawal status successfully updated.",
            "status" => "success",
            "customer" => $customer,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createBouns(Request $request)
    {
        $request->validate([
            "id" => "required|string|exists:customers,id",
            "amount" => "required|string",
        ]);
        $customer = Customer::where("id", $request->id)->first();
        $customer->deposit($request->amount);
        try {
            $customer->notify(new BonusDepositNotification($request->amount));
        } catch (\Throwable$th) {
            logs()->info($th);
        }

        return response()->json([
            "message" => "Deposit successful.",
            "status" => "success",
        ], 200);
    }

    public function createWithdrawal(Request $request)
    {
        $request->validate([
            "id" => "required|string|exists:customers,id",
            "amount" => "required|string",
        ]);
        $customer = Customer::where("id", $request->id)->first();

        if ($customer->customerbalance <= $request->amount) {
            return response()->json([
                "message" => "Account balance is low, fund account to continue.",
                "status" => "error",
            ], 400);
        }
        $customer->withdraw($request->amount);
        try {
            $customer->notify(new BonusWithdrawalNotification($request->amount, $customer->fullname));
        } catch (\Throwable$th) {
            logs()->info($th);
        }

        return response()->json([
            "message" => "Withdrawal successful.",
            "status" => "success",
        ], 200);
    }

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

    }

    public function logout()
    {

    }

    public function login(Request $request)
    {

    }

    public function show($id)
    {
        $customer = Customer::where("id", $id)->first();
        if (!$customer) {
            return response()->json([
                "message" => "No customer found.",
                "status" => "error",
            ], 400);
        }
        return response()->json([
            "message" => "Fetch successfully",
            "status" => "success",
            "customers" => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
