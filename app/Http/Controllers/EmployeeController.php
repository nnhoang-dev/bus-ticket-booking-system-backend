<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Mail\PasswordEmployee;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate;
use Ramsey\Uuid\Uuid;

class EmployeeController extends Controller
{
    public function index()
    {
        try {
            $employee = Employee::all();
            return response()->json(['employee' => $employee], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['message' => 'Not exist employee'], 404);
            }
            return response()->json(['employee' => $employee], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string',
                'email' => 'required|email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:0,1',
                'address' => 'required|string',
                'role' => 'required|in:manager,operator,customer_service,accountant,driver',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['message' => 'Not exist employee'], 404);
            }

            $data = $request->all();

            $employee->update($data);
            return response()->json(['message' => 'Update employee successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['message' => 'Not exist employee'], 404);
            }

            if ($employee->role == 'manager') {
                return response()->json(['message' => 'You do not have permission to delete manager'], 404);
            }


            $employee->delete();
            return response()->json(['message' => 'Delete employee successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Employee deletion is not allowed because they have assigned tasks', "exception" => $th], 500);
        }
    }

    public function getAllEmployeesByRole(string $role)
    {
        try {
            $employee = Employee::where('role', $role)->get();
            if (!$employee) {
                return response()->json(['message' => 'Not exist employee'], 404);
            }
            return EmployeeResource::collection($employee);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Register a Employee.
    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|unique:employees,phone_number',
                'email' => 'required|email|unique:employees,email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:0,1',
                'address' => 'required|string',
                'role' => 'required|string|in:manager,operator,customer_service,accountant,driver',

            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            // return response()->json(['message' => 'check'], 200);
            $employee = $request->all();
            $employee['id'] = Uuid::uuid4()->toString();
            $employee['password'] = mt_rand(10000000, 99999999);
            Mail::to($employee['email'])->send(new PasswordEmployee($employee['password']));


            Employee::create($employee);
            return response()->json(['message' => 'Add employee successfully'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function changeAvatar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|string',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $employee =  auth('employee_api')->user();
            $data = $request->all();

            $employee->update($data);
            return response()->json(['message' => 'Update avatar successfully', 'data' => $data], 200);
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


            $employee =  auth('employee_api')->user();
            $data = $request->all();

            $employee->update($data);
            return response()->json(['message' => 'Update employee successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }


    // public function register()
    // {
    //     $validator = Validator::make(request()->all(), [
    //         'first_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'phone_number' => 'required|string|regex:/^[0-9]{10,11}$/',
    //         'email' => 'required|email|unique:employees,email',
    //         'password' => 'required|confirmed|min:6',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors()->toJson(), 400);
    //     }

    //     $employee = request()->all();
    //     $employee['id'] = Uuid::uuid4();
    //     $employee['password'] = Hash::make($employee['password']);
    //     Employee::create($employee);


    //     return response()->json(
    //         [
    //             "message" => "Tạo tài khoản khách hàng successfully",
    //             "id" => $employee['id']
    //         ],
    //         201
    //     );
    // }

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
            return response()->json(['message' => 'You do not have access'], 401);
        }

        if (auth('employee_api')->user()->status == 0) {
            return response()->json(['message' => 'You do not have access'], 401);
        }

        return $this->respondWithToken($token);
    }


    public function changePassword()
    {
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


            $employee = auth('employee_api')->user();
            if (Hash::check(request()->current_password, $employee->password)) {
                $employee->password = request()->new_password;
                $employee->save();
                return response()->json(['message' => 'Change password successfully'], 200);
            } else {
                return response()->json(['message' => 'Failed to change password'], 400);
            }
            return response()->json(['message' => $employee], 200);
        } catch (\Throwable $th) {
            return response()->json("Server error", 500);
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
            'message' => 'Login successfully',
            'token' => $token,
            'employee' => auth('employee_api')->user(),
            'expires_in' => auth('employee_api')->factory()->getTTL()
        ]);
    }
}