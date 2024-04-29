<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\HoaDonResource;
use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class HoaDonController extends Controller
{
    public function index()
    {
        try {
            $hoaDon = HoaDon::all();
            return HoaDonResource::collection($hoaDon);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|digits_between:8,15|unique:khach_hang,phone_number',
                'email' => 'email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'discount' => 'integer',
                'price' => 'required|integer',
                'quantity' => 'required|integer',
                'total_price' => 'required|integer',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $data['id'] = Uuid::uuid4()->toString();

            HoaDon::create($data);
            return response()->json(['message' => 'Tạo hóa đơn thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $hoaDon = HoaDon::find($id);
            if (!$hoaDon) {
                return response()->json(['message' => 'Không tồn tại hóa đơn'], 404);
            }
            return new HoaDonResource($hoaDon);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|digits_between:8,15|unique:khach_hang,phone_number',
                'email' => 'email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'discount' => 'integer',
                'price' => 'required|integer',
                'quantity' => 'required|integer',
                'total_price' => 'required|integer',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            // find hóa đơn
            $hoaDon = HoaDon::find($id);
            if (!$hoaDon) {
                return response()->json(['message' => 'Không tồn tại hóa đơn'], 404);
            }

            $data = $request->all();
            $hoaDon->update($data);
            return response()->json(['message' => 'Cập nhật hóa đơn thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }



    public function destroy(string $id)
    {
        try {
            $hoaDon = HoaDon::find($id);
            if (!$hoaDon) {
                return response()->json(['message' => 'Không tồn tại hóa đơn'], 404);
            }

            $hoaDon->delete();
            return response()->json(['message' => 'Xóa hóa đơn thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}