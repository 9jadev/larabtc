<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customers\CreateCustomerRequest;
use App\Http\Requests\Customers\OtpLoginRequest;
use App\Http\Requests\Customers\UpdateKycRequest;
use App\Models\Customer;
use App\Models\Setting;
use App\Notifications\RefferalNotification;
use App\Notifications\ResetPassword;
use App\Notifications\SendOtpNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
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
    public function create(CreateCustomerRequest $request)
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

    public function refferaList()
    {
        $referral_code = auth()->user()->referral_code;
        $customers = Customer::where("sponsor_code", $referral_code)->latest()->paginate(request()->input("page_number"));

        return response()->json([
            "message" => "Fetched successfully.",
            "status" => "success",
            "customer" => $customers,
        ], 200);

    }

    public function resetPass()
    {
        if (empty(request()->input("email"))) {
            return response()->json([
                "message" => "Email is required.",
                "status" => "error",
            ], 400);
        }
        $customer = Customer::where("email", request()->input("email"))->first();
        if (!$customer) {
            return response()->json([
                "message" => "Customer doesn't exist.",
                "status" => "error",
            ], 400);
        }
        $otp = Str::random(7);
        logs()->info("ismkd $otp");
        $customer->update([
            'password' => bcrypt($otp),
        ]);
        $customer->notify(new ResetPassword($otp));
        return response()->json([
            "message" => "New password send to your email.",
            "status" => "success",
        ], 200);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:customers,email",
            "amount" => "required|string",
        ]);
        $reciver = Customer::where("email", $request->email)->first();
        if (auth()->user()->getKey() === $reciver->getKey()) {
            return response()->json([
                "message" => "You can't transfer to yourself account.",
                "status" => "error",
            ], 400);
        }
        if (auth()->user()->customerbalance < $request->amount) {
            return response()->json([
                "message" => "Account balance is low.",
                "status" => "error",
            ], 400);
        }

        auth()->user()->transfer($reciver, $request->amount);
        return response()->json([
            "message" => "Transfer completed.",
            "status" => "success",
        ], 200);

    }

    public function store($data)
    {
        $data = array_merge($data, [
            "sponsor_code" => isset($data["sponsor_code"]) ? $data["sponsor_code"] : null,
            'password' => bcrypt($data['password']),
            "image" => "https://via.placeholder.com/350x150",
            "referral_code" => Str::random(8),
        ]);
        $customer = Customer::create($data);
        $customer->notify(new WelcomeNotification($data));
        $this->payInterest($data["sponsor_code"]);
        if ($data["sponsor_code"] != null || $data["sponsor_code"] != "") {
            $this->payInterest($data["sponsor_code"]);
        }

        return response()->json([
            "message" => "Customer created successfully.",
            "status" => "success",
            "data" => $customer,
        ], 200);
    }

    private function payInterest($sponsor_code)
    {
        $customer = Customer::where("referral_code", $sponsor_code)->first();
        $settings = Setting::where("setting_code", "interest_code")->first();
        if ($customer) {
            $customer->notify(new RefferalNotification($settings->setting_amount));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()->json([
            "message" => "Fetched Successfully",
            "status" => "success",
            "customer" => auth()->user(),
        ], 200);
    }

    public function resetPasswordCustomer(Request $request)
    {
        //  bcrypt($otp),
        $oldpassword = request()->input("oldpassword");
        $newpassword = request()->input("newpassword");
        if (!$oldpassword) {
            return response()->json([
                "message" => "Old password is reqired.",
                "status" => "error",
            ]);
        }
        if (!$newpassword) {
            return response()->json([
                "message" => "New password is reqired.",
                "status" => "error",
            ]);
        }
        $customer = auth()->user();
        if (!$customer || !Hash::check($oldpassword, $customer->password)) {

            return response()->json([
                "message" => "The provided credentials are incorrect.",
                "status" => "error",
            ], 400);
        }
        $customer->update([
            "password" => bcrypt($newpassword),
        ]);

        return response()->json([
            "message" => "Password updated successfully.",
            "status" => "error",
        ], 400);

    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {

            return response()->json([
                "message" => "The provided credentials are incorrect.",
                "status" => "error",
            ], 400);
        }

        $otp = Str::random(7);

        $customer->update([
            "otp_code" => $otp,
        ]);

        $customer->notify(new SendOtpNotification());

        return response()->json([
            "message" => "OTP has been successfully sent to your email address",
            // 'customer' => $customer,
            "status" => "success",
            // 'token' => $customer->createToken('webapp', ['role:customer'])->plainTextToken,
        ]);
    }

    public function otPlogin(OtpLoginRequest $request)
    {
        $data = $request->validated();
        $customer = Customer::where('otp_code', $data["otp_code"])->first();
        if (!$customer) {
            return response()->json([
                "message" => "The otp code is incorrect.",
                "status" => "error",
            ], 400);
        }
        $customer->update(["otp_code" => null]);
        $customer->refresh();

        return response()->json([
            "message" => "Login successful.",
            'customer' => $customer,
            'token' => $customer->createToken('webapp', ['role:customer'])->plainTextToken,
        ]);
    }

    public function uploadKycImage()
    {
        $image = request()->input("id_card");
        if (!$image) {
            return response()->json([
                "message" => "Identity card is required.",
                "status" => "error",
            ], 400);
        }

        auth()->user()->update(["id_card" => $image]);
        $customer = auth()->user();

        return response()->json([
            "message" => "Identity card updated successfully.",
            "status" => "success",
            "custom" => $customer,
        ], 200);

    }

    public function uploadImage()
    {
        $image = request()->input("image");
        if (!$image) {
            return response()->json([
                "message" => "Image is required.",
                "status" => "error",
            ], 400);
        }

        auth()->user()->update(["image" => $image]);
        $customer = auth()->user();

        return response()->json([
            "message" => "Profile updated successfully.",
            "status" => "success",
            "custom" => $customer,
        ], 200);

    }

    public function updateProfile()
    {
        $fullname = request()->input("fullname");
        $email = request()->input("email");
        $phone = request()->input("phone_number");
        if (!$fullname) {
            return response()->json([
                "message" => "Fullname is required.",
                "status" => "error",
            ], 400);
        }
        if (!$email) {
            return response()->json([
                "message" => "Email is required.",
                "status" => "error",
            ], 400);
        }

        if (!$phone) {
            return response()->json([
                "message" => "Phone number is required.",
                "status" => "error",
            ], 400);
        }

        $customerEmail = Customer::where("id", "!=", auth()->user()->id)->where("email", $email)->first();

        if ($customerEmail) {
            return response()->json([
                "message" => "Email already exist.",
                "status" => "error",
            ], 400);
        }

        $customerPhone = Customer::where("id", "!=", auth()->user()->id)->where("phone_number", $phone)->first();

        if ($customerPhone) {
            return response()->json([
                "message" => "Phone number already exist.",
                "status" => "error",
            ], 400);
        }

        $cust = auth()->user();

        $cust->update([
            "fullname" => $fullname,
            "email" => $email,
            "phone_number" => $phone,
        ]);

        $cust->refresh();

        return response()->json([
            "message" => "Update successfully.",
            "status" => "success",
        ], 200);

    }

    public function updateKyc(UpdateKycRequest $request)
    {
        $data = $request->validated();
        $customer = auth()->user()->update([
            "image" => $data['image'],
            "id_card" => $data['id_card'],
        ]);
        // $customer->refresh();
        return response()->json([
            "message" => "Kyc update successful.",
            "status" => "success",
            "customer" => auth()->user(),
        ], 200);
    }

    public function logout()
    {
        $user = request()->user(); //or Auth::user()
        // Revoke current user token
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json([
            "message" => "Customer logout successfully.",
            "status" => "error",
        ]);
    }

    public function fundWallet()
    {
        if (empty(request()->input("amount"))) {
            return response()->json(["message" => "Amount is required.", "status" => "error"], 400);
        }
        if (empty(request()->input("customer_id"))) {
            return response()->json(["message" => "Customer is required.", "status" => "error"], 400);
        }
        $customer = Customer::where("id", request()->input("customer_id"))->first();
        $customer->deposit(request()->input("amount"), ["description" => "amount"]);
        $customer->refresh();
        return response()->json([
            "message" => "Amount successfully updated.",
            "status" => "success",
            "data" => $customer,
        ], 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json([
            "message" => "Deleted successfully.",
            "status" => "success",
            "data" => $customer,
        ], 200);
    }

}
