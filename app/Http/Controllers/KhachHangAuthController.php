<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmAccount;
use App\Models\KhachHang;
use App\Models\OTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;

class KhachHangAuthController extends Controller
{
    // Register a KhachHang.
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string|unique:khach_hang,phone_number|regex:/^[0-9]{10,11}$/',
            'email' => 'required|email|unique:khach_hang,email',
            'password' => 'required|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $khachHang = request()->all();
        $khachHang['id'] = Uuid::uuid4();
        $khachHang['password'] = Hash::make($khachHang['password']);
        KhachHang::create($khachHang);

        $otp = mt_rand(10000000, 99999999);
        OTP::create([
            'id' => Uuid::uuid4()->toString(),
            'khach_hang_id' => $khachHang['id'],
            'otp' => $otp,
        ]);
        Mail::to($khachHang['email'])->send(new ConfirmAccount($otp));

        return response()->json(
            [
                "message" => "Tạo tài khoản khách hàng thành công",
                "id" => $khachHang['id']
            ],
            201
        );
    }


    public function confirmEmail()
    {
        $validator = Validator::make(request()->all(), [
            'khach_hang_id' => 'required|string|exists:khach_hang,id',
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

        $time = $otp->created_at;
        $unixTimestamp = strtotime($time) + 60;

        if (time() > $unixTimestamp) {
            return response()->json(['message' => 'OTP đã hết hạn'], 404);
        } else {
            $khachHang = KhachHang::find(request()->khach_hang_id);
            $khachHang->update(["status" => 1]);
            $otp->delete();
            return response()->json(['message' => 'Xác thực thành công'], 200);
        }
    }

    public function sendBackConfirmEmail()
    {
        $validator = Validator::make(request()->all(), [
            'khach_hang_id' => 'required|string|exists:khach_hang,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $khachHang = KhachHang::find(request()->khach_hang_id);

        $otp = mt_rand(10000000, 99999999);
        OTP::create([
            'id' => Uuid::uuid4()->toString(),
            'khach_hang_id' => request()->khach_hang_id,
            'otp' => $otp,
        ]);

        Mail::to($khachHang['email'])->send(new ConfirmAccount($otp));

        return response()->json(['message' => 'Gửi OTP thành công'], 200);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['phone_number', 'password']);

        if (!$token = auth('khach_hang_api')->attempt($credentials)) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 401);
        }

        if (auth('khach_hang_api')->user()->status == 0) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 401);
        }

        return $this->respondWithToken($token);
        // $minutes = 60;
        // $path = "/";
        // $domain = "127.0.0.1";
        // ->cookie('token', $token, $minutes, $path, $domain, true, true);
    }


    public function changePassword()
    {
        // echo "hehe";
        try {
            $validator = Validator::make(request()->all(), [
                'password_old' => 'required|string',
                'password_new' => 'required|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }


            $khachHang = auth('khach_hang_api')->user();
            if (Hash::check(request()->password_old, $khachHang->password)) {
                $khachHang->password = request()->password_new;
                $khachHang->save();
                return response()->json("Thay đổi mật khẩu thành công", 200);
            } else {
                return response()->json("Thay đổi mật khẩu thất bại", 400);
            }
        } catch (\Throwable $th) {
            return response()->json("Lỗi ở phía máy chủ", 500);
        }
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            //code...
            return response()->json(["khachHang" => auth('khach_hang_api')->user()], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["error" => $th], 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('khach_hang_api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('employee_api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'infor' => auth('khach_hang_api')->user(),
            'expires_in' => auth('khach_hang_api')->factory()->getTTL()
        ]);
    }
}
