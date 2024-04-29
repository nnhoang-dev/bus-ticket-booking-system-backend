<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Models\NhaXe;
use App\Models\TuyenXe;

class TuyenXeController extends Controller
{
    public function index()
    {
        try {
            $tuyenXe = TuyenXe::with(['start_address', 'end_address'])->get();
            return response()->json($tuyenXe, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_address' => 'required|string',
                'end_address' => 'required|string',
                'time' => 'required|date_format:H:i:s'
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $start_address = NhaXe::find($data['start_address']);
            $end_address = NhaXe::find($data['end_address']);
            if (
                !$start_address || !$end_address
                || ($start_address['status'] == 0)
                || ($end_address['status'] == 0)
                || ($start_address == $end_address)
            ) {
                return response()->json(["message" => "Tạo tuyến xe không thành công do địa chỉ nhà xe không hợp lệ"], 400);
            }

            $data['id'] = Uuid::uuid4()->toString();

            TuyenXe::create($data);
            return response()->json(['message' => 'Tạo tuyến xe thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function show(string $id)
    {
        try {
            $tuyenXe = TuyenXe::with(['start_address', 'end_address'])->find($id);
            if (!$tuyenXe) {
                return response()->json(['message' => 'Không tồn tại tuyến xe'], 404);
            }
            return response()->json($tuyenXe, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_address' => 'string',
                'end_address' => 'string',
                'time' => 'date_format:H:i:s',
                'status' => 'in:0,1',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $tuyenXe = TuyenXe::find($id);
            if (!$tuyenXe) {
                return response()->json(['message' => 'Không tồn tại tuyến xe'], 404);
            }

            $data = $request->all();
            $start_address = NhaXe::find($data['start_address']);
            $end_address = NhaXe::find($data['end_address']);
            if (
                !$start_address || !$end_address
                || ($start_address['status'] == 0)
                || ($end_address['status'] == 0)
                || ($start_address == $end_address)
            ) {
                return response()->json(["message" => "Tạo tuyến xe không thành công do địa chỉ nhà xe không hợp lệ"], 400);
            }
            $tuyenXe->update($data);
            return response()->json(['message' => 'Cập nhật tuyến xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $tuyenXe = TuyenXe::find($id);
            if (!$tuyenXe) {
                return response()->json(['message' => 'Không tồn tại tuyến xe'], 404);
            }

            $tuyenXe->delete();
            return response()->json(['message' => 'Xóa tuyến xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}