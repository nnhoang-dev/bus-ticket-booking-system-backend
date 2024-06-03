<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

use App\Models\Trip;
use App\Models\Employee;
use App\Models\Route;

class TripController extends Controller
{
    // Get all trips information
    public function index()
    {
        try {
            $trips = Trip::with(['route.start_address', 'route.end_address', 'bus'])->get();
            return response()->json($trips, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Get a trip by id
    public function show(string $id)
    {
        try {
            $trip = Trip::with(['route.start_address', 'route.end_address', 'bus'])->find($id);
            // $trip = Trip::find($id);
            if (!$trip) {
                return response()->json(['message' => 'Not exist trip'], 404);
            }
            return response()->json(["trip" => $trip], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Get trips same route
    public function getTripSameRoute()
    {
        try {
            $trip = Trip::with(['route.start_address', 'route.end_address', 'bus'])->where('route_id', request()->route_id)->get();
            return response()->json(["trip" => $trip], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Create a new trip
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'route_id' => 'required|string|exists:routes,id',
                'bus_id' => 'required|string|exists:buses,id',
                'driver_id' => 'required|string|exists:employees,id',
                'date' => 'required|string',
                'start_time' => 'required|date_format:H:i:s',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }

            $data = $request->all();
            $driver = Employee::find($data['driver_id']);
            $route = Route::find($data['route_id']);
            $bus = Bus::find($data['bus_id']);
            if (
                !$route || !$bus || !$driver
                || ($route['status'] == 0)
                || ($bus['status'] == 0)
                || ($driver['status'] == 0)
                || ($driver['role'] != "driver")
            ) {
                return response()->json(["message" => "The bus station address was invalid"], 400);
            }

            // auto generated end_time
            $startTime = Carbon::createFromTimeString($data['start_time']);
            $time = Carbon::createFromTimeString($route['time']);
            $endTime = $startTime->addHour($time->hour)->addMinutes($time->minute)->addSeconds($time->second);


            $data['id'] = Uuid::uuid4()->toString();
            $data['price'] = $route->price;
            $data['end_time'] = $endTime;
            Trip::create($data);
            return response()->json(['message' => 'Add trip successfully'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Update a trip information
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'route_id' => 'string|exists:routes,id',
                'bus_id' => 'string|exists:buses,id',
                'driver_id' => 'string|exists:employees,id',
                'date' => 'string',
                'start_time' => 'date_format:H:i:s',
                'status' => 'in:1,0'
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $error) {
                    return response()->json(["message" => $error], 400);
                }
            }


            $trip = Trip::find($id);
            if (!$trip) {
                return response()->json(['message' => 'Not exist trip'], 404);
            }

            if ($trip->seat) {
                return response()->json(['message' => 'You do not have permission to edit this trip'], 401);
            }

            $data = $request->all();
            if (isset($data['route_id'])) {
                $driver = Employee::find($data['driver_id']);
                $route = Route::find($data['route_id']);
                $bus = Bus::find($data['bus_id']);
                if (
                    !$route || !$bus || !$driver
                    || ($route['status'] == 0)
                    || ($bus['status'] == 0)
                    || ($driver['status'] == 0)
                    || ($driver['role'] != "driver")
                ) {
                    return response()->json(["message" => "The information was invalid"], 400);
                }

                // auto generated end_time
                $startTime = Carbon::createFromTimeString($data['start_time']);
                $time = Carbon::createFromTimeString($route['time']);
                $endTime = $startTime->addHour($time->hour)->addMinutes($time->minute)->addSeconds($time->second);
                $data['end_time'] = $endTime;
            }

            $trip->update($data);
            return response()->json(['message' => 'Update trip successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // Delete a trip by id
    public function destroy(string $id)
    {
        try {
            $trip = Trip::find($id);
            if (!$trip) {
                return response()->json(['message' => 'Not exist trip'], 404);
            }
            if ($trip->seat) {
                return response()->json(['message' => 'You do not have permission to delete this trip'], 401);
            }

            $trip->delete();
            return response()->json(['message' => 'Delete trip successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }
}