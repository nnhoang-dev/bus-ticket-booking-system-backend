<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Http\Resources\NhaXeResource;
use App\Models\NhaXe;

class NhaXeController extends Controller
{
    public function index()
    {
        $xe = NhaXe::all();
        return NhaXeResource::collection($xe);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string|unique:nha_xe,address',
            'phone_number' => 'required|string|unique:nha_xe,phone_number',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {

            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $data = $request->all();
        $data['id'] = Uuid::uuid4()->toString();

        NhaXe::create($data);
        return response()->json(['message' => 'Tạo nhà xe thành công'], 201);
    }


    public function show(string $id)
    {
        $xe = NhaXe::find($id);
        if (!$xe) {
            return response()->json(['message' => 'Không tìm thấy nhà xe'], 404);
        }
        return new NhaXeResource($xe);
    }


    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string|unique:nha_xe,address',
            'phone_number' => 'required|string|unique:nha_xe,phone_number',
            'status' => 'in:0,1',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }


        $xe = NhaXe::find($id);
        if (!$xe) {
            return response()->json(['message' => 'Không tìm thấy nhà xe'], 404);
        }

        $data = $request->all();

        $xe->update($data);
        return response()->json(['message' => 'Cập nhật nhà xe thành công'], 200);
    }


    function destroy(string $id)
    {
        $xe = NhaXe::find($id);
        if (!$xe) {
            return response()->json(['message' => 'Không tìm thấy nhà xe'], 404);
        }

        $xe->delete();
        return response()->json(['message' => 'Xóa nhà xe thành công'], 200);
    }
}
