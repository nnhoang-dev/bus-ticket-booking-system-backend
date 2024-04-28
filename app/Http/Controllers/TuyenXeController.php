<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Http\Resources\TuyenXeResource;
use App\Models\NhaXe;
use App\Models\TuyenXe;

class TuyenXeController extends Controller
{
    public function index()
    {
        $tuyenXe = TuyenXe::with(['start_address', 'end_address'])->get();
        return response()->json($tuyenXe, 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'start_address' => 'required|string',
            'end_address' => 'required|string',
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
        try {
            TuyenXe::create($data);
            return response()->json(['message' => 'Tạo tuyến xe thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Tạo tuyến xe không thành công'], 400);
        }
    }

    public function show(string $id)
    {
        $tuyenXe = TuyenXe::find($id);
        if (!$tuyenXe) {
            return response()->json(['message' => 'Không tìm thấy tuyến xe'], 404);
        }
        return new TuyenXeResource($tuyenXe);
    }

    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'start_address' => 'required|string',
            'end_address' => 'required|string',
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
            return response()->json(['message' => 'Không tìm thấy tuyến xe'], 404);
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
    }

    public function destroy(string $id)
    {
        $tuyenXe = TuyenXe::find($id);
        if (!$tuyenXe) {
            return response()->json(['message' => 'Không tìm thấy tuyến xe'], 404);
        }

        $tuyenXe->delete();
        return response()->json(['message' => 'Xóa tuyến xe thành công'], 200);
    }
}
