<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmAccount;
use App\Mail\PasswordCustomer;
use App\Models\Customer;
use App\Models\OTP;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function validateUpdate($customer, $request)
    {
        if ($customer->email != $request->email) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:customers,email',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }
        }
        if ($customer->phone_number != $request->phone_number) {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|unique:customers,phone_number',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }
        }
        return true;
    }


    public function index()
    {
        try {
            $customer = Customer::all();
            return response()->json(['customer' => $customer], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $customer = Customer::find($id);
            if (!$customer) {
                return response()->json(['message' => 'Not exist customer'], 404);
            }
            return response()->json(['customer' => $customer], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|unique:customers,phone_number',
                'email' => 'required|email|unique:customers,email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:0,1',
                'address' => 'required|string',

            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $customer = $request->all();
            $customer['id'] = Uuid::uuid4()->toString();
            $customer['password'] = mt_rand(10000000, 99999999);
            $customer['status'] = 1;
            Mail::to($customer['email'])->send(new PasswordCustomer($customer['password']));


            Customer::create($customer);
            return response()->json(['message' => 'Add customer successfully'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:0,1',
                'address' => 'required|string',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $customer = Customer::find($id);
            if (!$customer) {
                return response()->json(['message' => 'Not exist customer'], 404);
            }
            $validPhoneEmail = $this->validateUpdate($customer, $request);
            // return response()->json(['message' => ($validPhoneEmail !== true)], 200);
            if ($validPhoneEmail !== true) {
                return  $validPhoneEmail;
            }

            $data = $request->all();

            $customer->update($data);
            return response()->json(['message' => 'Update customer successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $customer = Customer::find($id);
            if (!$customer) {
                return response()->json(['message' => 'Not exist customer'], 404);
            }

            $customer->delete();
            return response()->json(['message' => 'Delete customer successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function updateMyAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:0,1',
                'address' => 'required|string',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $customer =  auth('customer_api')->user();
            $data = $request->all();

            $customer->update($data);
            return response()->json(['message' => 'Update customer successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Register a Customer.
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string|unique:customers,phone_number|regex:/^[0-9]{10,11}$/',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|confirmed|min:6',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $customer = request()->all();
        $customer['id'] = Uuid::uuid4();
        $customer['password'] = Hash::make($customer['password']);
        Customer::create($customer);

        $otp = mt_rand(10000000, 99999999);
        OTP::create([
            'id' => Uuid::uuid4()->toString(),
            'customer_id' => $customer['id'],
            'otp' => $otp,
        ]);
        Mail::to($customer['email'])->send(new ConfirmAccount($otp, 'confirm-account'));

        return response()->json(
            [
                "message" => "Tạo tài khoản khách hàng thành công",
                "id" => $customer['id']
            ],
            201
        );
    }


    public function confirmEmail()
    {
        $validator = Validator::make(request()->all(), [
            'customer_id' => 'required|string|exists:customers,id',
            'otp' => 'required|string|exists:otps,otp',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $otp = OTP::where('customer_id', request()->customer_id)
            ->where('otp', request()->otp)->first();
        if (!$otp) {
            return response()->json(['message' => 'Mã xác thực không chính xác'], 404);
        }

        $time = $otp->created_at;
        $unixTimestamp = strtotime($time) + 60;

        if (time() > $unixTimestamp) {
            return response()->json(['message' => 'OTP đã hết hạn'], 404);
        } else {
            $customer = Customer::find(request()->customer_id);
            $customer->update(["status" => 1]);
            $otp->delete();
            return response()->json(['message' => 'Xác thực thành công'], 200);
        }
    }

    public function resendComfirmEmail()
    {
        $validator = Validator::make(request()->all(), [
            'customer_id' => 'required|string|exists:customers,id',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $customer = Customer::find(request()->customer_id);

        $otp = mt_rand(10000000, 99999999);
        OTP::create([
            'id' => Uuid::uuid4()->toString(),
            'customer_id' => request()->customer_id,
            'otp' => $otp,
        ]);

        Mail::to($customer['email'])->send(new ConfirmAccount($otp, 'confirm-account'));

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

        if ((!$token = auth('customer_api')->attempt($credentials)) || auth('customer_api')->user()->status == 0) {
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
                'current_password' => 'required|string',
                'new_password' => 'required|confirmed',
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $customer = auth('customer_api')->user();
            if (Hash::check(request()->current_password, $customer->password)) {
                $customer->password = request()->new_password;
                $customer->save();
                return response()->json(["message" => "Thay đổi mật khẩu thành công"], 200);
            } else {
                return response()->json(["message" => "Thay đổi mật khẩu thất bại"], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => "Server error"], 500);
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
            return response()->json(["customer" => auth('customer_api')->user()], 200);
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
        auth('customer_api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('customer_api')->refresh());
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
            'infor' => auth('customer_api')->user(),
            'expires_in' => auth('customer_api')->factory()->getTTL()
        ]);
    }
}