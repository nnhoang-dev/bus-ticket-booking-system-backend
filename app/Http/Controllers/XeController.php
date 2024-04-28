<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Http\Resources\XeResource;
use App\Models\Xe;

class XeController extends Controller
{
    public function index()
    {
        $xe = Xe::all();
        return XeResource::collection($xe);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license' => 'required|string|unique:xe,license',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {

            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $data = $request->all();
        $data['kh_id'] = Uuid::uuid4()->toString();

        Xe::create($data);
        return response()->json(['message' => 'Tạo xe thành công'], 201);
    }

    public function show(string $id)
    {
        $xe = Xe::find($id);
        if (!$xe) {
            return response()->json(['message' => 'Không tìm thấy xe'], 404);
        }
        return new XeResource($xe);
    }

    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'license' => 'required|string|unique:xe,license',
            'status' => 'in:0,1',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }


        // find xe
        $xe = Xe::find($id);
        if (!$xe) {
            return response()->json(['message' => 'Không tìm thấy xe'], 404);
        }

        $data = $request->all();

        $xe->update($data);
        return response()->json(['message' => 'Cập nhật xe thành công'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $xe = Xe::find($id);
        if (!$xe) {
            return response()->json(['message' => 'Không tìm thấy xe'], 404);
        }

        $xe->delete();
        return response()->json(['message' => 'Xóa xe thành công'], 200);
    }
}
