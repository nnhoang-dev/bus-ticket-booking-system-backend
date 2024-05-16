<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BusStation;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Trip;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function get()
    {
        try {
            // $ticket = Ticket::with(['chuyen_xe.tuyen_xe.start_address', 'chuyen_xe.tuyen_xe.end_address', 'chuyen_xe.xe'])->get();
            $ticket = Ticket::all();
            return response()->json($ticket, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }

    // public function checkTicket(Request $request)
    // {
    //     $tickets = "asdsd,dasdsa";
    //     Mail::to('nnhoanghd2004@gmail.com')->send(new ResultTicket($tickets));
    //     return response()->json(['message' => 'Gửi mail thành công'], 200);
    // }


    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'trip_id' => 'required|string|exists:chuyen_xe,id',
    //         'khach_hang_id' => 'required|string|exists:khach_hang,id',
    //         'hoa_don_id' => 'required|string|exists:hoa_don,id',
    //         'first_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'phone_number' => 'required|string',
    //         'route_name' => 'required|string|max:255',
    //         'date' => 'required|date',
    //         'start_time' => 'required|date_format:H:i:s',
    //         'end_time' => 'required|date_format:H:i:s|after:start_time',
    //         'start_address' => 'required|string',
    //         'end_address' => 'required|string',
    //         'seat' => 'required|string',
    //         'price' => 'required|integer',
    //         'license' => 'required|string',
    //     ]);
    //     if ($validator->stopOnFirstFailure()->fails()) {
    //         $errors = $validator->errors();
    //         foreach ($errors->all() as $error) {
    //             return response()->json(["message" => $error], 400);
    //         }
    //     }

    //     $data = $request->all();
    //     $chuyenXe = ChuyenXe::find($data['trip_id']);
    //     if (
    //         !$chuyenXe
    //         || ($chuyenXe['status'] == 0)
    //     ) {
    //         return response()->json(["message" => "Tạo ticket  không thành công do thông tin không hợp lệ"], 400);
    //     }

    //     $data['id'] = Uuid::uuid4()->toString();
    //     Ticket::create($data);
    //     return response()->json(['message' => 'Tạo chuyến xe thành công'], 201);
    //     try {
    //     } catch (\Throwable $th) {
    //         return response()->json(['message' => 'Server error', "exception" => $th], 500);
    //     }
    // }


    public function lookupTicket(Request $request)
    {
        $ticket_id = $request->ticket_id;
        try {
            $ticket = Ticket::where('ticket_id', $ticket_id)->first();
            if (!$ticket) {
                return response()->json(['message' => 'Invalid phone number or ticket code'], 400);
            }
            //check phone_number
            if ($ticket->phone_number) {
                return response()->json($ticket, 200);
            }
            return response()->json(['message' => 'Invalid phone number or ticket code'], 400);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }


    private function updateChuyenXe($trip_id,  $newSeat, $oldTrip, $oldSeat)
    {
        try {
            $seats = explode(",", $oldTrip->seat);
            $seatsNew = array_filter($seats, function ($seat) use ($oldSeat) {
                return $seat != $oldSeat;
            });
            $seatsNew = join(",", $seatsNew);
            $oldTrip->update(["seat" => $seatsNew]);

            $newTrip = Trip::with(['route.start_address', 'route.end_address', 'bus'])->find($trip_id);

            $seatsNew = "";
            if ($newTrip->seat == "") {
                $seatsNew = $newSeat;
            } else {
                $seats = explode(",", $newTrip->seat);
                array_push($seats, $newSeat);
                $seatsNew = join(",", $seats);
            }

            $newTrip->update(["seat" => $seatsNew]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    private function updateTicket($newTrip, $ticket, $seat)
    {
        try {
            $route = $newTrip->route;
            $bus = $newTrip->bus;
            $route_name = $route->name;
            $date = $newTrip->date;
            $start_time = $newTrip->start_time;
            $end_time = $newTrip->end_time;
            $start_address = BusStation::find($route->start_address)->address;
            $end_address = BusStation::find($route->end_address)->address;
            $price = $newTrip->price;
            $license = $bus->license;

            $data = [
                "trip_id" => $newTrip->id,
                "route_name" => $route_name,
                "date" => $date,
                "start_time" => $start_time,
                "end_time" => $end_time,
                "start_address" => $start_address,
                "end_address" => $end_address,
                "seat" => $seat,
                "price" => $price,
                "license" => $license,
            ];

            $ticket->update($data);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function changeTicket(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|string|exists:trips,id',
            'seat' => 'required'
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $ticket = Ticket::find($id);

        // Kiểm tra vé tồn tại
        if (!$ticket) {
            return response()->json(['message' => 'Not exist ticket'], 404);
        }

        $data = $request->all();

        $trip_id = $data['trip_id'];
        $oldTrip = Trip::with(['route.start_address', 'route.end_address', 'bus'])->find($ticket->trip_id);
        $newTrip = Trip::with(['route.start_address', 'route.end_address', 'bus'])->find($trip_id);
        $oldSeat = $ticket->seat;
        $newSeat = $data['seat'];

        // Kiểm tra phải trùng tuyến xe
        if ($newTrip->route_id != $oldTrip->route_id) {
            return response()->json(['message' => 'Incorrect bus route'], 404);
        }

        // Kiểm tra ghế trống
        if (in_array($newSeat, explode(',', $newTrip->seat))) {
            return response()->json(['message' => 'The seat is reserved'], 404);
        }

        // cập nhật thông tin ticket
        if ($this->updateTicket($newTrip, $ticket, $newSeat) == false) {
            return response()->json(['message' => 'Server error'], 500);
        }

        // cập nhật thông tin chuyến xe ( ghế ngồi )
        if ($this->updateChuyenXe($trip_id, $newSeat, $oldTrip, $oldSeat) == false) {
            return response()->json(['message' => 'Server error'], 500);
        }


        return response()->json(['message' => 'Chance ticket successfully'], 200);
        try {
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $ticket = Ticket::find($id);
            if (!$ticket) {
                return response()->json(['message' => 'Not exist ticket'], 404);
            }
            $trip_id = $ticket->trip_id;
            $seat = $ticket->seat;

            $trip = Trip::find($trip_id);
            if (!$trip) {
                return response()->json(['message' => 'Not exist trip'], 404);
            }
            // $seatsNew = array_filter($seats, function ($seat) use ($oldSeat) {
            //     return $seat != $oldSeat;
            // });
            $tripSeats = $trip->seat;
            $tripSeats = explode(",", $tripSeats);

            $newStripSeats = array_filter($tripSeats, function ($tripSeat) use ($seat) {
                return $tripSeat != $seat;
            });
            $newStripSeats = join(",", $newStripSeats);

            $trip->update(["seat" => $newStripSeats]);
            $ticket->delete();
            return response()->json(['message' => 'Ticket successfully cancelled'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Server error', "exception" => $th], 500);
        }
    }
};
