<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmAccount;
use App\Models\NhanVien;
use App\Models\OTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;

class NhanVienAuthController extends Controller
{
    // Register a NhanVien.
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string|regex:/^[0-9]{10,11}$/',
            'email' => 'required|email|unique:nhan_vien,email',
            'password' => 'required|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $nhanVien = request()->all();
        $nhanVien['id'] = Uuid::uuid4();
        $nhanVien['password'] = Hash::make($nhanVien['password']);
        NhanVien::create($nhanVien);


        return response()->json(
            [
                "message" => "Tạo tài khoản khách hàng thành công",
                "id" => $nhanVien['id']
            ],
            201
        );
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['phone_number', 'password']);

        if (!$token = auth('nhan_vien_api')->attempt($credentials)) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 401);
        }

        if (auth('nhan_vien_api')->user()->status == 0) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 401);
        }

        return $this->respondWithToken($token);
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


            $khachHang = auth('nhan_vien_api')->user();
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
            return response()->json(["nhanVien" => auth('nhan_vien_api')->user()], 200);
        } catch (\Throwable $th) {
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
        auth('nhan_vien_api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth('employee_api')->refresh());
    // }

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
            'nhanVien' => auth('nhan_vien_api')->user(),
            'expires_in' => auth('nhan_vien_api')->factory()->getTTL()
        ]);
    }
}
