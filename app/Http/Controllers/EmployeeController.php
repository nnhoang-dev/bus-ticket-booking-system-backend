<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate;
use Ramsey\Uuid\Uuid;

class EmployeeController extends Controller
{
    public function getAllEmployeeByRole(string $role)
    {
        try {
            $nhanViens = Employee::where('role', $role)->get();
            if (!$nhanViens) {
                return response()->json(['message' => 'Không tồn tại nhân viên'], 404);
            }
            return EmployeeResource::collection($nhanViens);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
    // Register a Employee.
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|unique:employees,phone_number',
                'password' => 'required|string|min:6',
                'email' => 'required|email|unique:employees,email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:0,1',
                'address' => 'required|string',
                'role' => 'required|in:QL,VH,CS,KT,TX',

            ]);
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $data['id'] = Uuid::uuid4()->toString();
            $data['password'] = Hash::make($data['password']);

            Employee::create($data);
            return response()->json(['message' => 'Tạo nhân viên thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string|regex:/^[0-9]{10,11}$/',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $employee = request()->all();
        $employee['id'] = Uuid::uuid4();
        $employee['password'] = Hash::make($employee['password']);
        Employee::create($employee);


        return response()->json(
            [
                "message" => "Tạo tài khoản khách hàng thành công",
                "id" => $employee['id']
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
        // return response()->json(["message" => "hehe"], 200);
        $credentials = request(['phone_number', 'password']);

        if (!$token = auth('employee_api')->attempt($credentials)) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 401);
        }

        if (auth('employee_api')->user()->status == 0) {
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


            $employee = auth('employee_api')->user();
            if (Hash::check(request()->password_old, $employee->password)) {
                $employee->password = request()->password_new;
                $employee->save();
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
            return response()->json(["employee" => auth('employee_api')->user()], 200);
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
        auth('employee_api')->logout();

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
            'employee' => auth('employee_api')->user(),
            'expires_in' => auth('employee_api')->factory()->getTTL()
        ]);
    }
}