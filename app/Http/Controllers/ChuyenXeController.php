<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

use App\Models\ChuyenXe;
use App\Models\NhanVien;
use App\Models\TuyenXe;
use App\Models\Xe;

class ChuyenXeController extends Controller
{
    public function index()
    {
        try {
            $chuyenXe = ChuyenXe::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->get();
            return response()->json($chuyenXe, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tuyen_xe_id' => 'required|string',
                'xe_id' => 'required|string',
                'tai_xe_id' => 'required|string',
                'date' => 'required|string',
                'start_time' => 'required|string',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $taiXe = NhanVien::find($data['tai_xe_id']);
            $tuyenXe = TuyenXe::find($data['tuyen_xe_id']);
            $xe = Xe::find($data['xe_id']);
            if (
                !$tuyenXe || !$xe || !$taiXe
                || ($tuyenXe['status'] == 0)
                || ($xe['status'] == 0)
                || ($taiXe['status'] == 0)
            ) {
                return response()->json(["message" => "1Tạo chuyến xe không thành công do địa chỉ nhà xe không hợp lệ"], 400);
            }

            // auto generated end_time
            $startTime = Carbon::createFromTimeString($data['start_time']);
            $time = Carbon::createFromTimeString($tuyenXe['time']);
            $endTime = $startTime->addHour($time->hour)->addMinutes($time->minute)->addSeconds($time->second);


            $data['id'] = Uuid::uuid4()->toString();
            $data['end_time'] = $endTime;
            ChuyenXe::create($data);
            return response()->json(['message' => 'Tạo chuyến xe thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function show(string $id)
    {
        try {
            $chuyenXe = ChuyenXe::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->find($id);
            if (!$chuyenXe) {
                return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
            }
            return response()->json($chuyenXe, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tuyen_xe_id' => 'string',
                'xe_id' => 'string',
                'date' => 'string',
                'start_time' => 'string'
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $chuyenXe = ChuyenXe::find($id);
            if (!$chuyenXe) {
                return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
            }

            $data = $request->all();
            $taiXe = NhanVien::find($data['tai_xe_id']);
            $tuyenXe = TuyenXe::find($data['tuyen_xe_id']);
            $xe = Xe::find($data['xe_id']);
            if (
                !$tuyenXe || !$xe || !$taiXe
                || ($tuyenXe['status'] == 0)
                || ($xe['status'] == 0)
                || ($taiXe['status'] == 0)
            ) {
                return response()->json(["message" => "Tạo chuyến xe không thành công do thông tin không hợp lệ"], 400);
            }

            // auto generated end_time
            $startTime = Carbon::createFromTimeString($data['start_time']);
            $time = Carbon::createFromTimeString($tuyenXe['time']);
            $endTime = $startTime->addHour($time->hour)->addMinutes($time->minute)->addSeconds($time->second);
            $data['end_time'] = $endTime;

            $chuyenXe->update($data);
            return response()->json(['message' => 'Cập nhật chuyến xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $chuyenXe = ChuyenXe::find($id);
            if (!$chuyenXe) {
                return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
            }

            $chuyenXe->delete();
            return response()->json(['message' => 'Xóa chuyến xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}