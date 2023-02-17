<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paymentType = PaymentType::latest();
        request()->input('isdefault') ? $paymentType->where("isdefault", request()->input('isdefault')) : $paymentType;
        $data = request()->input('page_number') ? $paymentType->paginate(request()->input('page_number')) : $paymentType->get();
        return response()->json([
            "message" => "Fetched successfully.",
            "status" => "success",
            "data" => $data,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
            "payment_type_name" => "required|string",
            "value" => "required|string",
        ]);

        $data = [
            "payment_type_code" => Str::of($request->payment_type_name)->lower(),
            "payment_type_name" => Str::of($request->payment_type_name)->upper(),
            "payment_type_image" => null,
            "isdefault" => 0,
            "value" => $request->value,
        ];
        $paymentType = PaymentType::create($data);
        return response()->json([
            "message" => "Created successfully.",
            "status" => "success",
            "data" => $paymentType,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentType $paymentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentType $paymentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentType $paymentType)
    {
        $request->validate([
            "payment_type_name" => "required|string",
            "value" => "required|string",
        ]);

        $data = [
            "payment_type_code" => Str::of($request->payment_type_name)->lower(),
            "payment_type_name" => Str::of($request->payment_type_name)->upper(),
            "payment_type_image" => null,
            "isdefault" => 0,
            "value" => $request->value,
        ];
        $paymentType->update($data);
        return response()->json([
            "message" => "updated successfully.",
            "status" => "success",
            "data" => $paymentType,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentType $paymentType)
    {
        $paymentType->delete();
        return response()->json([
            "message" => "deleted successfully.",
            "status" => "success",
            "data" => $paymentType,
        ], 200);
    }
}
