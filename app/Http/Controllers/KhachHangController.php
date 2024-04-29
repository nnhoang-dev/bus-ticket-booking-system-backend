<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\KhachHangResource;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class KhachHangController extends Controller
{
    public function index()
    {
        try {
            $khachHang = KhachHang::all();
            return KhachHangResource::collection($khachHang);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|digits_between:8,15|unique:khach_hang,phone_number',
                'password' => 'required|string|min:6',
                'email' => 'required|email|unique:khach_hang,email',
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

            $data = $request->all();
            $data['id'] = Uuid::uuid4()->toString();
            $data['password'] = Hash::make($data['password']);

            KhachHang::create($data);
            return response()->json(['message' => 'Tạo khách hàng thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $khachHang = KhachHang::find($id);
            if (!$khachHang) {
                return response()->json(['message' => 'Không tồn tại khách hàng'], 404);
            }
            return new KhachHangResource($khachHang);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            if ($request->isMethod('PUT')) {
                // validaion
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'date_of_birth' => 'required|date',
                    'gender' => 'required|in:0,1',
                    'address' => 'required|string',
                    'status' => 'in:0,1',
                ]);
            } else if ($request->isMethod('PATCH')) {
                // validaion
                $validator = Validator::make($request->all(), [
                    'phone_number' => 'string|digits_between:8,15|unique:khach_hang,phone_number',
                    'password' => 'string|min:6',
                    'email' => 'email|unique:khach_hang,email',
                    'status' => 'in:0,1',

                ]);
            }
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            // find khach_hang
            $khachHang = KhachHang::find($id);
            if (!$khachHang) {
                return response()->json(['message' => 'Không tồn tại khách hàng'], 404);
            }

            $data = $request->all();
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $khachHang->update($data);
            return response()->json(['message' => 'Cập nhật khách hàng thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }



    public function destroy(string $id)
    {
        try {
            $khachHang = KhachHang::find($id);
            if (!$khachHang) {
                return response()->json(['message' => 'Không tồn tại khách hàng'], 404);
            }

            $khachHang->delete();
            return response()->json(['message' => 'Xóa khách hàng thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}