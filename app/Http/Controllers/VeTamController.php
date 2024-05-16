<?php

namespace App\Http\Controllers;

use App\Models\ChuyenXe;
use App\Models\VeTam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class VeTamController extends Controller
{
    public function index()
    {
        try {
            // $veTam = VeTam::with(['chuyen_xe.tuyen_xe.start_address', 'chuyen_xe.tuyen_xe.end_address', 'chuyen_xe.xe'])->get();
            $veTam = VeTam::all();
            return response()->json($veTam, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chuyen_xe_id' => 'required|string|exists:chuyen_xe,id',
            'khach_hang_id' => 'required|string|exists:khach_hang,id',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $data = $request->all();
        $chuyenXe = ChuyenXe::find($data['chuyen_xe_id']);
        if (
            !$chuyenXe
            || ($chuyenXe['status'] == 0)
        ) {
            return response()->json(["message" => "Tạo vé tạm không thành công do thông tin không hợp lệ"], 400);
        }

        $data['id'] = Uuid::uuid4()->toString();
        VeTam::create($data);
        return response()->json(['message' => 'Tạo chuyến vé tạm thành công'], 201);
        try {
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }


    public function show(string $id)
    {
        try {
            // $chuyenXe = VeTam::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->find($id);
            $veTam = VeTam::find($id);
            if (!$veTam) {
                return response()->json(['message' => 'Not exist chuyến xe'], 404);
            }
            return response()->json($veTam, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }


    // public function update(Request $request, string $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'chuyen_xe_id' => 'string|exists:chuyen_xe,id',
    //         'khach_hang_id' => 'string|exists:khach_hang,id',
    //         'hoa_don_id' => 'string|exists:hoa_don,id',
    //         'route_name' => 'string|max:255',
    //         'date' => 'date',
    //         'start_time' => 'date_format:H:i:s',
    //         'end_time' => 'date_format:H:i:s|after:start_time',
    //         'start_address' => 'string',
    //         'end_address' => 'string',
    //         'seat' => 'string|max:5',
    //         'price' => 'integer',
    //         'license' => 'string',
    //         // 'status' => 'in:0,1',
    //     ]);
    //     if ($validator->stopOnFirstFailure()->fails()) {
    //         $errors = $validator->errors();
    //         foreach ($errors->all() as $error) {
    //             return response()->json(["message" => $error], 400);
    //         }
    //     }


    //     $veTam = VeTam::find($id);
    //     if (!$veTam) {
    //         return response()->json(['message' => 'Not exist chuyến xe'], 404);
    //     }

    //     $data = $request->all();
    //     // $khachHang = KhachHang::find($data['khach_hang_id']);
    //     // $chuyenXe = ChuyenXe::find($data['chuyen_xe_id']);
    //     // $hoaDon = HoaDon::find($data['hoa_don_id']);
    //     // if (
    //     //     !$chuyenXe || !$hoaDon || !$khachHang
    //     //     || ($chuyenXe['status'] == 0)
    //     //     || ($khachHang['status'] == 0)
    //     // ) {
    //     //     return response()->json(["message" => "Tạo vé xe không thành công do thông tin không hợp lệ"], 400);
    //     // }
    //     if ($veTam['status'] != "no") {
    //         return response()->json(["message" => "Đặt vé xe không thành công do vé đã được đặt"], 400);
    //     }


    //     $veTam->update($data);


    //     $veTam = VeTam::find($id);
    //     if (!$veTam) {
    //         return response()->json(['message' => 'Not exist chuyến xe'], 404);
    //     }
    //     if ($veTam['status'] == "pending") {
    //         $veTam->update(["status" => 'no']);
    //     }
    //     return response()->json(['message' => 'Cập nhật chuyến xe thành công'], 200);
    //     try {
    //     } catch (\Throwable $th) {
    //         return response()->json(['message' => 'Server error', "exception" => $th], 500);
    //     }
    // }


    // public function destroy(string $id)
    // {
    //     try {
    //         $veTam = VeTam::find($id);
    //         if (!$veTam) {
    //             return response()->json(['message' => 'Not exist chuyến xe'], 404);
    //         }

    //         $veTam->delete();
    //         return response()->json(['message' => 'Xóa chuyến xe thành công'], 200);
    //     } catch (\Throwable $th) {
    //         return response()->json(['message' => 'Server error', "exception" => $th], 500);
    //     }
    // }
}
