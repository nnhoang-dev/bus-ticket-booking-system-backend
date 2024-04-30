<?php

namespace App\Http\Controllers;

use App\Models\ChuyenXe;
use App\Models\KhachHang;
use App\Models\VeTam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class DatVeController extends Controller
{
    private function checkSeat($seat, $chuyenXe)
    {
        $seatRequest = explode(",", $seat);
        $seatChuyenXe = explode(",", $chuyenXe['seat']);

        foreach ($seatRequest as $seat) {
            if ((!is_numeric($seat)) || (intval($seat) < 1) || (intval($seat) > 34)) {
                return false;
            }
        }

        foreach ($seatRequest as $seat) {
            if (in_array($seat, $seatChuyenXe)) {
                return false;
            }
        }
        return true;
    }

    private function createVeTam($data, $chuyenXe)
    {
        $seats = explode(",", $data['seat']);
        foreach ($seats as $seat) {
            $veTam = $data;
            $veTam['seat'] = $seat;
            $veTam['id'] = Uuid::uuid4();
            VeTam::create($veTam);
        }

        $seat = $chuyenXe['seat'];
        if ($seat == "") {
            $seat = $seat . $data['seat'];
        } else {
            $seat = $seat . "," . $data['seat'];
        }

        $chuyenXe->update([
            "seat" => $seat,
        ]);
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chuyen_xe_id' => 'required|string|exists:chuyen_xe,id',
                'khach_hang_id' => 'required|string|exists:khach_hang,id',
                'seat' => 'required',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $chuyenXe = ChuyenXe::find($data['chuyen_xe_id']);

            if ($this->checkSeat($data['seat'], $chuyenXe) == false) {
                return response()->json(["message" => "Đặt vé xe không thành công do vé đã được đặt"], 400);
            }

            $res = $this->createVeTam($data, $chuyenXe);
            // new chuyen xe
            $res = ChuyenXe::find($data['chuyen_xe_id']);

            return response()->json(["message" => "Đặt vé tạm thành công", "response" => $res], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}
