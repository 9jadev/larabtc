<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payments\CreatePaymentRequest;
use App\Models\Payment;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment_types = PaymentType::whereNotNull("payment_types.id")->latest()->get();

        foreach ($payment_types as $value) {
            $payments = Payment::where("payment_types_id", $value->id)->where("customer_id", auth()->user()->id)->first();
            $value["payment"] = $payments;
        }
        return response()->json([
            "message" => "Fetched successfully",
            "status" => "success",
            "payment_type" => $payment_types,
        ], 200);
    }

    public function indexList()
    {
        $id = request()->input("id");
        $payments = Payment::where("customer_id", $id)->get();
        foreach ($payments as $value) {
            $payment_types = PaymentType::where("id", $value->payment_types_id)->first();
            $value["payment_types"] = $payment_types;
        }
        return response()->json([
            "message" => "Fetched successfully",
            "status" => "success",
            "payment_type" => $payments,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreatePaymentRequest $request)
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

        $payment = Payment::updateOrCreate(
            [
                'payment_types_id' => $data["payment_types_id"],
                "customer_id" => auth()->user()->id,
            ],
            $data
        );
        return response()->json([
            "message" => "Payment type created succesfully.",
            "status" => "success",
            "payment" => $payment,
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
