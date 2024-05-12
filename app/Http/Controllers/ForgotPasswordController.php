<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmAccount;
use App\Models\KhachHang;
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

        $khachHang = KhachHang::where('phone_number', request()->account)->first();
        if (!$khachHang) {
            $khachHang = KhachHang::where('email', request()->account)->first();
        }
        if (!$khachHang) {
            return response()->json(['message' => 'Email hoặc số điện thoại không chính xác'], 400);
        }
        $otp = mt_rand(10000000, 99999999);
        OTP::create([
            'id' => Uuid::uuid4()->toString(),
            'khach_hang_id' => $khachHang['id'],
            'otp' => $otp,
        ]);
        Mail::to($khachHang['email'])->send(new ConfirmAccount($otp, 'forgot-password'));

        return response()->json([
            'message' => 'Gửi OTP thành công',
            "khach_hang_id" => $khachHang['id']
        ], 200);
    }

    public function changePasswordForgotPassword()
    {
        $validator = Validator::make(request()->all(), [
            'khach_hang_id' => 'required|string|exists:khach_hang,id',
            'password' =>   'required|string|confirmed',
            'otp' => 'required|string|exists:otp,otp',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $otp = OTP::where('khach_hang_id', request()->khach_hang_id)
            ->where('otp', request()->otp)->first();
        if (!$otp) {
            return response()->json(['message' => 'Mã xác thực không chính xác'], 404);
        }

        // check timestamp otp
        $time = $otp->created_at;
        $unixTimestamp = strtotime($time) + 120;
        if (time() > $unixTimestamp) {
            return response()->json(['message' => 'OTP đã hết hạn'], 404);
        } else {

            $khachHang = KhachHang::find(request()->khach_hang_id);
            $khachHang->update(["password" => Hash::make(request()->password)]);
            $otp->delete();
            return response()->json(['message' => request()->password], 200);
        }
    }
}
