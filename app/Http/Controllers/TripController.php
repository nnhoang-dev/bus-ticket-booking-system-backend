<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

use App\Models\Trip;
use App\Models\Employee;
use App\Models\Route;

class TripController extends Controller
{
    public function index()
    {
        try {
            $trips = Trip::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->get();
            return response()->json($trips, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tuyen_xe_id' => 'required|string|exists:tuyen_xe,id',
                'xe_id' => 'required|string|exists:xe,id',
                'tai_xe_id' => 'required|string|exists:nhan_vien,id',
                'date' => 'required|string',
                'start_time' => 'required|date_format:H:i:s',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $driver = Employee::find($data['tai_xe_id']);
            $route = Route::find($data['tuyen_xe_id']);
            $bus = Bus::find($data['xe_id']);
            if (
                !$route || !$bus || !$driver
                || ($route['status'] == 0)
                || ($bus['status'] == 0)
                || ($driver['status'] == 0)
                || ($driver['role'] != "TX")
            ) {
                return response()->json(["message" => "Tạo chuyến xe không thành công do địa chỉ nhà xe không hợp lệ"], 400);
            }

            // auto generated end_time
            $startTime = Carbon::createFromTimeString($data['start_time']);
            $time = Carbon::createFromTimeString($route['time']);
            $endTime = $startTime->addHour($time->hour)->addMinutes($time->minute)->addSeconds($time->second);


            $data['id'] = Uuid::uuid4()->toString();
            $data['price'] = $route->price;
            $data['end_time'] = $endTime;
            Trip::create($data);
            return response()->json(['message' => 'Tạo chuyến xe thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function show(string $id)
    {
        try {
            $trip = Trip::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->find($id);
            if (!$trip) {
                return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
            }
            return response()->json($trip, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tuyen_xe_id' => 'required|string|exists:tuyen_xe,id',
                'xe_id' => 'required|string|exists:xe,id',
                'tai_xe_id' => 'required|string|exists:nhan_vien,id',
                'date' => 'required|string',
                'start_time' => 'required|date_format:H:i:s',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $trip = Trip::find($id);
            if (!$trip) {
                return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
            }

            if ($trip->seat) {
                return response()->json(['message' => 'Bạn không có quyền chỉnh sửa chuyến xe này'], 401);
            }

            $data = $request->all();
            $driver = Employee::find($data['tai_xe_id']);
            $route = Route::find($data['tuyen_xe_id']);
            $bus = Bus::find($data['xe_id']);
            if (
                !$route || !$bus || !$driver
                || ($route['status'] == 0)
                || ($bus['status'] == 0)
                || ($driver['status'] == 0)
                || ($driver['role'] != "TX")
            ) {
                return response()->json(["message" => "Tạo chuyến xe không thành công do thông tin không hợp lệ"], 400);
            }

            // auto generated end_time
            $startTime = Carbon::createFromTimeString($data['start_time']);
            $time = Carbon::createFromTimeString($route['time']);
            $endTime = $startTime->addHour($time->hour)->addMinutes($time->minute)->addSeconds($time->second);
            $data['end_time'] = $endTime;

            $trip->update($data);
            return response()->json(['message' => 'Cập nhật chuyến xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $trip = Trip::find($id);
            if (!$trip) {
                return response()->json(['message' => 'Không tồn tại chuyến xe'], 404);
            }
            if ($trip->seat) {
                return response()->json(['message' => 'Bạn không có quyền xóa chuyến xe này'], 401);
            }

            $trip->delete();
            return response()->json(['message' => 'Xóa chuyến xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function getTripWithRoute()
    {
        try {
            $trip = Trip::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->where('tuyen_xe_id', request()->tuyen_xe_id)->get();
            return response()->json(["chuyenXe" => $trip], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}