<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Http\Resources\BusResource;
use App\Models\Bus;

class BusController extends Controller
{
    public function index()
    {
        try {
            $xe = Bus::all();
            return BusResource::collection($xe);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'license' => 'required|string|unique:buses,license',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $data['id'] = Uuid::uuid4()->toString();
            Bus::create($data);
            return response()->json(['message' => 'Thêm xe thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $xe = Bus::find($id);
            if (!$xe) {
                return response()->json(['message' => 'Không tồn tại xe'], 404);
            }
            return new BusResource($xe);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'license' => 'string|unique:buses,license'
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            // find bus
            $bus = Bus::find($id);
            if (!$bus) {
                return response()->json(['message' => 'Không tồn tại xe'], 404);
            }

            $data = $request->all();

            $bus->update($data);
            return response()->json(['message' => 'Cập nhật xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $xe = Bus::find($id);
            if (!$xe) {
                return response()->json(['message' => 'Không tồn tại xe'], 404);
            }

            $xe->delete();
            return response()->json(['message' => 'Xóa xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}
