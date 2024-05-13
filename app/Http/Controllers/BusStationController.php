<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Http\Resources\BusStationResource;
use App\Models\BusStation;

class BusStationController extends Controller
{
    public function index()
    {
        try {
            $busStation = BusStation::all();
            return BusStationResource::collection($busStation);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'city' => 'required|string',
                'address' => 'required|string|unique:bus_stations,address',
                'phone_number' => 'required|string|unique:bus_stations,phone_number',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $data['id'] = Uuid::uuid4()->toString();


            BusStation::create($data);
            return response()->json(['message' => 'Tạo nhà xe thành công'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function show(string $id)
    {
        try {
            $busStation = BusStation::find($id);
            if (!$busStation) {
                return response()->json(['message' => 'Không tồn tại nhà xe'], 404);
            }
            return new BusStationResource($busStation);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string',
                'city' => 'string',

                'address' => 'string',
                'phone_number' => 'string',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $busStation = BusStation::find($id);
            if (!$busStation) {
                return response()->json(['message' => 'Không tồn tại nhà xe'], 404);
            }

            $data = $request->all();

            $busStation->update($data);
            return response()->json(['message' => 'Cập nhật nhà xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }


    function destroy(string $id)
    {
        try {
            $busStation = BusStation::find($id);
            if (!$busStation) {
                return response()->json(['message' => 'Không tồn tại nhà xe'], 404);
            }

            $busStation->delete();
            return response()->json(['message' => 'Xóa nhà xe thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
        }
    }
}