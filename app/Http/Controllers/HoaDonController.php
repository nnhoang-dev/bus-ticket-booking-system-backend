<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\HoaDonResource;
use App\Models\HoaDon;
use App\Models\KhachHang;
use Illuminate\Http\Request;
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
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function store($request)
    {
        $validator = Validator::make($request, [
            'khach_hang_id' => 'required|string|exists:khach_hang,id',
            'giao_dich_id' => 'required|string|exists:giao_dich,id',
            'phone_number' => 'required|string|digits_between:8,15|unique:khach_hang,phone_number',
            'email' => 'email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'discount' => 'integer',
            'price' => 'required|integer',
            'quantity' => 'required|integer',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {

            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        return $request;
        // try {
        //     $validator = Validator::make($request->all(), [
        //         'khach_hang_id' => 'required|string',
        //         'phone_number' => 'required|string|digits_between:8,15|unique:khach_hang,phone_number',
        //         'email' => 'email',
        //         'first_name' => 'required|string|max:255',
        //         'last_name' => 'required|string|max:255',
        //         'discount' => 'integer',
        //         'price' => 'required|integer',
        //         'quantity' => 'required|integer',
        //     ]);
        //     if ($validator->stopOnFirstFailure()->fails()) {

        //         $errors = $validator->errors();
        //         foreach ($errors->all() as $error) {
        //             return response()->json(["message" => $error], 400);
        //         }
        //     }

        //     $data = $request->all();
        //     $khachHang = KhachHang::find($data['khach_hang_id']);
        //     if (!$khachHang || ($khachHang['status'] == 0)) {
        //         return response()->json(["message" => "Tạo hóa đơn không thành công do thông tin không hợp lệ"], 400);
        //     }
        //     $data['id'] = Uuid::uuid4()->toString();
        //     if (isset($data['discount'])) {
        //         $data['total_price'] = $data['price'] * $data['quantity'] * (1 - ($data['discount'] / 100));
        //     } else {
        //         $data['total_price'] = $data['price'] * $data['quantity'];
        //     }

        //     HoaDon::create($data);
        //     return response()->json(['message' => 'Tạo hóa đơn thành công'], 201);
        // } catch (\Throwable $th) {
        //     return response()->json(['message' => 'Server error', "exception" => $th], 500);
        // }
    }

    public function show(string $id)
    {
        try {
            $hoaDon = HoaDon::find($id);
            if (!$hoaDon) {
                return response()->json(['message' => 'Not exist hóa đơn'], 404);
            }
            return new HoaDonResource($hoaDon);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'khach_hang_id' => 'required|string',
                'phone_number' => 'required|string|digits_between:8,15|unique:khach_hang,phone_number',
                'email' => 'email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'discount' => 'integer',
                'price' => 'required|integer',
                'quantity' => 'required|integer',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }
            $data = $request->all();
            $khachHang = KhachHang::find($data['khach_hang_id']);
            if (!$khachHang || ($khachHang['status'] == 0)) {
                return response()->json(["message" => "Tạo hóa đơn không thành công do thông tin không hợp lệ"], 400);
            }


            // find hóa đơn
            $hoaDon = HoaDon::find($id);
            if (!$hoaDon) {
                return response()->json(['message' => 'Not exist hóa đơn'], 404);
            }

            // tinh tong gia
            if (isset($data['discount'])) {
                $data['total_price'] = $data['price'] * $data['quantity'] * (1 - ($data['discount'] / 100));
            } else {
                $data['total_price'] = $data['price'] * $data['quantity'];
            }

            $hoaDon->update($data);
            return response()->json(['message' => 'Cập nhật hóa đơn thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }



    public function destroy(string $id)
    {
        try {
            $hoaDon = HoaDon::find($id);
            if (!$hoaDon) {
                return response()->json(['message' => 'Not exist hóa đơn'], 404);
            }

            $hoaDon->delete();
            return response()->json(['message' => 'Xóa hóa đơn thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }
}
