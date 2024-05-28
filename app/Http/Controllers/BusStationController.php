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
    // Get all bus stations information
    public function index()
    {
        try {
            $busStation = BusStation::all();
            return BusStationResource::collection($busStation);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Create a new bus station
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
            return response()->json(['message' => 'Add bus station successfully'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Get a bus station by id
    public function show(string $id)
    {
        try {
            $busStation = BusStation::find($id);
            if (!$busStation) {
                return response()->json(['message' => 'Not exist bus station'], 404);
            }
            return new BusStationResource($busStation);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Update a bus station
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
                return response()->json(['message' => 'Not exist bus station'], 404);
            }

            $data = $request->all();

            $busStation->update($data);
            return response()->json(['message' => 'Update bus station successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Delete a bus station
    function destroy(string $id)
    {
        try {
            $busStation = BusStation::find($id);
            if (!$busStation) {
                return response()->json(['message' => 'Not exist bus station'], 404);
            }

            $busStation->delete();
            return response()->json(['message' => 'Delete bus station successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }
}
