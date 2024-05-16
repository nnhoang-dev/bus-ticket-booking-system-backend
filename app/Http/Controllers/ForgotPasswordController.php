<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmAccount;
use App\Models\Customer;
use App\Models\OTP;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class ForgotPasswordController extends Controller
{
    public function sendOTPForgotPassword()
    {
        $validator = Validator::make(request()->all(), [
            'account' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $customer = Customer::where('phone_number', request()->account)->first();
        if (!$customer) {
            $customer = Customer::where('email', request()->account)->first();
        }
        if (!$customer) {
            return response()->json(['message' => 'Email hoặc số điện thoại không chính xác'], 400);
        }
        $otp = mt_rand(10000000, 99999999);
        OTP::create([
            'id' => Uuid::uuid4()->toString(),
            'customer_id' => $customer['id'],
            'otp' => $otp,
        ]);
        Mail::to($customer['email'])->send(new ConfirmAccount($otp, 'forgot-password'));

        return response()->json([
            'message' => 'Gửi OTP thành công',
            "customer_id" => $customer['id']
        ], 200);
    }

    public function changePasswordForgotPassword()
    {
        $validator = Validator::make(request()->all(), [
            'customer_id' => 'required|string|exists:customers,id',
            'password' =>   'required|string|confirmed',
            'otp' => 'required|string|exists:otps,otp',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->toJson()], 400);
        }

        $otp = OTP::where('customer_id', request()->customer_id)
            ->where('otp', request()->otp)->first();
        if (!$otp) {
            return response()->json(['message' => 'Mã xác thực không chính xác'], 404);
        }

        // check timestamp otp
        $time = $otp->created_at;
        $unixTimestamp = strtotime($time) + 480;
        if (time() > $unixTimestamp) {
            return response()->json(['message' => 'OTP đã hết hạn'], 404);
        } else {
            $customer = Customer::find(request()->customer_id);
            $customer->update(["password" => Hash::make(request()->password)]);
            $otp->delete();
            return response()->json(['message' => 'Đổi mật khẩu thành công'], 200);
        }
    }
}
