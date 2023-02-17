<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            "email" => "required|email|unique:admin,email",
            "password" => "required|string",
        ]);
        $admin = Admin::create([
            "email" => $request->email,
            "password" => $request->password,
        ]);
        return response()->json([
            'admin' => $admin,
            'status' => "success",
            "message" => "Successfully created",
        ]);

    }

    public function logout()
    {
        $user = request()->user(); //or Auth::user()
        // Revoke current user token
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json([
            "message" => "Admin logout successfully.",
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $customer = Admin::where('email', $request->email)->first();

        if (!$customer || ($request->password != $customer->password)) {
            return response()->json([
                "message" => "The provided credentials are incorrect.",
                "status" => "error",
            ], 400);
        }

        return response()->json([
            'admin' => $customer,
            'token' => $customer->createToken('webapp', ['role:admin'])->plainTextToken,
        ]);

    }

    public function showProfile()
    {
        return response()->json([
            "message" => "Fetched Successfully",
            "status" => "success",
            "admin" => auth()->user(),
        ], 200);
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
