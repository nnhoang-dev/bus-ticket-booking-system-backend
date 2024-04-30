<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChuyenXe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Models\VeXe;
use Illuminate\Support\Facades\Schedule;

class VeXeController extends Controller
{
    public function index()
    {
        try {
            // $veXe = VeXe::with(['chuyen_xe.tuyen_xe.start_address', 'chuyen_xe.tuyen_xe.end_address', 'chuyen_xe.xe'])->get();
            $veXe = VeXe::all();
            return response()->json($veXe, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chuyen_xe_id' => 'required|string|exists:chuyen_xe,id',
            'khach_hang_id' => 'string|exists:khach_hang,id',
            'hoa_don_id' => 'string|exists:hoa_don,id',
            'route_name' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'start_address' => 'required|string',
            'end_address' => 'required|string',
            'seat' => 'required|string|max:5',
            'price' => 'required|integer',
            'license' => 'required|string',
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
            return response()->json(["message" => "Tạo vé xe không thành công do thông tin không hợp lệ"], 400);
        }

        $data['id'] = Uuid::uuid4()->toString();
        VeXe::create($data);
        return response()->json(['message' => 'Tạo chuyến xe thành công'], 201);
        try {
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function show(string $id)
    {
        try {
            // $chuyenXe = VeXe::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->find($id);
            $veXe = VeXe::find($id);
            if (!$veXe) {
                return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
            }
            return response()->json($veXe, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'chuyen_xe_id' => 'string|exists:chuyen_xe,id',
            'khach_hang_id' => 'string|exists:khach_hang,id',
            'hoa_don_id' => 'string|exists:hoa_don,id',
            'route_name' => 'string|max:255',
            'date' => 'date',
            'start_time' => 'date_format:H:i:s',
            'end_time' => 'date_format:H:i:s|after:start_time',
            'start_address' => 'string',
            'end_address' => 'string',
            'seat' => 'string|max:5',
            'price' => 'integer',
            'license' => 'string',
            // 'status' => 'in:0,1',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }


        $veXe = VeXe::find($id);
        if (!$veXe) {
            return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
        }

        $data = $request->all();
        // $khachHang = KhachHang::find($data['khach_hang_id']);
        // $chuyenXe = ChuyenXe::find($data['chuyen_xe_id']);
        // $hoaDon = HoaDon::find($data['hoa_don_id']);
        // if (
        //     !$chuyenXe || !$hoaDon || !$khachHang
        //     || ($chuyenXe['status'] == 0)
        //     || ($khachHang['status'] == 0)
        // ) {
        //     return response()->json(["message" => "Tạo vé xe không thành công do thông tin không hợp lệ"], 400);
        // }
        if ($veXe['status'] != "no") {
            return response()->json(["message" => "Đặt vé xe không thành công do vé đã được đặt"], 400);
        }


        $veXe->update($data);


        $veXe = VeXe::find($id);
        if (!$veXe) {
            return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
        }
        if ($veXe['status'] == "pending") {
            $veXe->update(["status" => 'no']);
        }
        return response()->json(['message' => 'Cập nhật chuyến xe thành công'], 200);
        try {
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $veXe = VeXe::find($id);
            if (!$veXe) {
                return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
            }

            $veXe->delete();
            return response()->json(['message' => 'Xóa chuyến xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}
