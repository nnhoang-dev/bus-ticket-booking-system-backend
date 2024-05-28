<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use App\Models\BusStation;
use App\Models\Route;

class RouteController extends Controller
{
    // Get all routes information
    public function index()
    {
        try {
            $route = Route::with(['start_address', 'end_address'])->get();
            return response()->json(["route" => $route], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Create a new route
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_address' => 'required|exists:bus_stations,id',
                'end_address' => 'required|exists:bus_stations,id',
                'time' => 'required|date_format:H:i:s',
                'price' => 'required|integer'
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {

                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $start_address = BusStation::find($data['start_address']);
            $end_address = BusStation::find($data['end_address']);
            if (
                !$start_address || !$end_address
                || ($start_address['status'] == 0)
                || ($end_address['status'] == 0)
                || ($start_address == $end_address)
            ) {
                return response()->json(["message" => "The bus station address was invalid"], 400);
            }
            $start_address = BusStation::find($data['start_address']);
            $end_address = BusStation::find($data['end_address']);
            $data['name'] = $start_address->city . " - " . $end_address->city;
            $data['id'] = Uuid::uuid4()->toString();
            Route::create($data);
            return response()->json(['message' => 'Add route successfully'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Get a route information by id
    public function show(string $id)
    {
        try {
            $route = Route::with(['start_address', 'end_address'])->find($id);
            if (!$route) {
                return response()->json(['message' => 'Not exist route'], 404);
            }
            return response()->json(["route" => $route], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Update a route infomanation
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_address' => 'string|exists:bus_stations,id',
                'end_address' => 'string|exists:bus_stations,id',
                'name' => 'string',
                'time' => 'date_format:H:i:s',
                'price' => 'integer',
                'status' => 'in:0,1',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $route = Route::find($id);
            if (!$route) {
                return response()->json(['message' => 'Not exist route'], 404);
            }

            $data = $request->all();
            $start_address = BusStation::find($data['start_address']);
            $end_address = BusStation::find($data['end_address']);
            if (
                !$start_address || !$end_address
                || ($start_address['status'] == 0)
                || ($end_address['status'] == 0)
                || ($start_address == $end_address)
            ) {
                return response()->json(["message" => "The bus station address was invalid"], 400);
            }
            $route->update($data);
            return response()->json(['message' => 'Update route successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Delete a route
    public function destroy(string $id)
    {
        try {
            $route = Route::find($id);
            if (!$route) {
                return response()->json(['message' => 'Not exist route'], 404);
            }

            $route->delete();
            return response()->json(['message' => 'Delete route successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }
}
