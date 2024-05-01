<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChuyenXe;
use App\Models\NhaXe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Models\VeXe;
use Illuminate\Support\Facades\Schedule;

class VeXeController extends Controller
{
    public function get()
    {
        try {
            // $veXe = VeXe::with(['chuyen_xe.tuyen_xe.start_address', 'chuyen_xe.tuyen_xe.end_address', 'chuyen_xe.xe'])->get();
            $veXe = VeXe::all();
            return response()->json($veXe, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'chuyen_xe_id' => 'required|string|exists:chuyen_xe,id',
    //         'khach_hang_id' => 'required|string|exists:khach_hang,id',
    //         'hoa_don_id' => 'required|string|exists:hoa_don,id',
    //         'first_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'phone_number' => 'required|string',
    //         'route_name' => 'required|string|max:255',
    //         'date' => 'required|date',
    //         'start_time' => 'required|date_format:H:i:s',
    //         'end_time' => 'required|date_format:H:i:s|after:start_time',
    //         'start_address' => 'required|string',
    //         'end_address' => 'required|string',
    //         'seat' => 'required|string',
    //         'price' => 'required|integer',
    //         'license' => 'required|string',
    //     ]);
    //     if ($validator->stopOnFirstFailure()->fails()) {
    //         $errors = $validator->errors();
    //         foreach ($errors->all() as $error) {
    //             return response()->json(["message" => $error], 400);
    //         }
    //     }

    //     $data = $request->all();
    //     $chuyenXe = ChuyenXe::find($data['chuyen_xe_id']);
    //     if (
    //         !$chuyenXe
    //         || ($chuyenXe['status'] == 0)
    //     ) {
    //         return response()->json(["message" => "Tạo vé xe không thành công do thông tin không hợp lệ"], 400);
    //     }

    //     $data['id'] = Uuid::uuid4()->toString();
    //     VeXe::create($data);
    //     return response()->json(['message' => 'Tạo chuyến xe thành công'], 201);
    //     try {
    //     } catch (\Throwable $th) {
    //         return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
    //     }
    // }


    public function getVeXeById(string $id)
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


    private function updateChuyenXe($chuyenXeNew,  $seatNew, $chuyenXeOld, $seatOld)
    {
        $seats = explode(",", $chuyenXeOld->seat);
        $seatsNew = array_filter($seats, function ($seat) use ($seatOld) {
            return $seat != $seatOld;
        });
        $seatsNew = join(",", $seatsNew);
        $chuyenXeOld->update(["seat" => $seatsNew]);


        $seatsNew = "";
        if ($chuyenXeNew->seat == "") {
            $seatsNew = $seatNew;
        } else {
            $seats = explode(",", $chuyenXeNew->seat);
            array_push($seats, $seatNew);
            $seatsNew = join(",", $seats);
        }
        $chuyenXeNew->update(["seat" => $seatsNew]);
        return $seatsNew;
    }


    private function updateVeXe($chuyenXeNew, $veXe, $seat)
    {
        try {
            $tuyenXe = $chuyenXeNew->tuyen_xe;
            $xe = $chuyenXeNew->xe;
            $route_name = $tuyenXe->name;
            $date = $chuyenXeNew->date;
            $start_time = $chuyenXeNew->start_time;
            $end_time = $chuyenXeNew->end_time;
            $start_address = NhaXe::find($tuyenXe->start_address)->address;
            $end_address = NhaXe::find($tuyenXe->end_address)->address;
            $price = $chuyenXeNew->price;
            $license = $xe->license;

            $data = [
                "chuyen_xe_id" => $chuyenXeNew->id,
                "route_name" => $route_name,
                "date" => $date,
                "start_time" => $start_time,
                "end_time" => $end_time,
                "start_address" => $start_address,
                "end_address" => $end_address,
                "seat" => $seat,
                "price" => $price,
                "license" => $license,
            ];

            $veXe->update($data);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function changeVeXe(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'chuyen_xe_id' => 'required|string|exists:chuyen_xe,id',
            'seat' => 'required|string'
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $veXe = VeXe::find($id);

        // Kiểm tra vé tồn tại
        if (!$veXe) {
            return response()->json(['message' => 'Không tồn tại vé xe'], 404);
        }

        // Kiểm tra không trùng chuyến xe
        $data = $request->all();
        if ($veXe->chuyen_xe_id == $data['chuyen_xe_id']) {
            return response()->json(['message' => 'Đổi trùng chuyến xe'], 404);
        }

        $chuyen_xe_id = $data['chuyen_xe_id'];
        $chuyenXeOld = ChuyenXe::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->find($veXe->chuyen_xe_id);
        $chuyenXeNew = ChuyenXe::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->find($chuyen_xe_id);

        // Kiểm tra phải trùng tuyến xe
        if ($chuyenXeNew->tuyen_xe_id != $chuyenXeOld->tuyen_xe_id) {
            return response()->json(['message' => 'Không trùng tuyến xe'], 404);
        }

        // Kiểm tra ghế trống
        if (in_array($data['seat'], explode(',', $chuyenXeNew->seat))) {
            return response()->json(['message' => 'Ghế đã được đặt'], 404);
        }

        // cập nhật thông tin vé xe
        if ($this->updateVeXe($chuyenXeNew, $veXe, $data['seat'])) {
            return response()->json(['message' => 'Lỗi ở phía server'], 500);
        }

        // cập nhật thông tin chuyến xe ( ghế ngồi )
        if ($this->updateChuyenXe($chuyenXeNew, $data['seat'], $chuyenXeOld, $veXe->seat)) {
            return response()->json(['message' => 'Lỗi ở phía server'], 500);
        }

        return response()->json(['message' => 'Đổi vé thành công'], 200);
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
