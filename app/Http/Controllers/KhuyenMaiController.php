<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Http\Resources\KhuyenMaiResource;
use App\Models\KhuyenMai;

class KhuyenMaiController extends Controller
{
    public function index()
    {
        try {
            $khuyenMai = KhuyenMai::all();
            return KhuyenMaiResource::collection($khuyenMai);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|string|unique:khuyen_mai,id',
                'discount' => 'required|integer',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            KhuyenMai::create($data);
            return response()->json(['message' => 'Tạo khuyến mãi thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $khuyenMai = KhuyenMai::find($id);
            if (!$khuyenMai) {
                return response()->json(['message' => 'Không tồn tại khuyến mãi'], 404);
            }
            return new KhuyenMaiResource($khuyenMai);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|string|unique:khuyen_mai,id',
                'discount' => 'required|integer',
                'status' => 'in:0,1',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            // find khuyến mãi
            $khuyenMai = KhuyenMai::find($id);
            if (!$khuyenMai) {
                return response()->json(['message' => 'Không tồn tại khuyến mãi'], 404);
            }

            $data = $request->all();

            $khuyenMai->update($data);
            return response()->json(['message' => 'Cập nhật khuyến mãi thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $khuyenMai = KhuyenMai::find($id);
            if (!$khuyenMai) {
                return response()->json(['message' => 'Không tồn tại khuyến mãi'], 404);
            }

            $khuyenMai->delete();
            return response()->json(['message' => 'Xóa khuyến mãi thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}