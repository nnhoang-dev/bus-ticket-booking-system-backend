<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Mail\ResultTicket;
use App\Models\Customer;
use App\Models\Bus;
use App\Models\BusStation;
use App\Models\Invoice;
use App\Models\TemporaryTicket;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class PaymentController extends Controller
{
    private function checkSeat($seat, $trip)
    {
        $seatRequest = explode(",", $seat);
        $seatTrip = explode(",", $trip['seat']);

        foreach ($seatRequest as $seat) {
            if ((!is_numeric($seat)) || (intval($seat) < 1) || (intval($seat) > 36)) {
                return false;
            }
        }

        foreach ($seatRequest as $seat) {
            if (in_array($seat, $seatTrip)) {
                return false;
            }
        }
        return true;
    }


    private function createTemporaryTicket($data, $trip)
    {
        $seats = explode(",", $data['seat']);
        $res = "";
        foreach ($seats as $seat) {
            $temporaryTicket = $data;
            $temporaryTicket['seat'] = $seat;
            $temporaryTicket['id'] = Uuid::uuid4();
            TemporaryTicket::create($temporaryTicket);
            $res = $res . $temporaryTicket['id'] . ",";
        }
        $res = substr($res, 0, -1);

        $seat = $trip['seat'];
        if ($seat == "") {
            $seat = $seat . $data['seat'];
        } else {
            $seat = $seat . "," . $data['seat'];
        }

        $trip->update([
            "seat" => $seat,
        ]);

        return $res;
    }


    private function order($request)
    {
        try {
            $validator = Validator::make($request, [
                'trip_id' => 'required|string|exists:trips,id',
                'customer_id' => 'required|string|exists:customers,id',
                'seat' => 'required',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return 'validate';
            }

            $data = $request;
            $trip = Trip::find($data['trip_id']);
            if ($this->checkSeat($data['seat'], $trip) == false) {
                return 'checkSeat';
            }

            $res = $this->createTemporaryTicket($data, $trip);
            return $res;
        } catch (\Throwable $th) {
            return 'catch';
        }
    }



    public function post(Request $request)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $data = $request->all();

        $temporaryTicket = [
            "trip_id" => $data['trip_id'],
            "customer_id" => $data['customer_id'],
            "seat" => $data['seat']
        ];

        $seat = $this->order($temporaryTicket);
        // return response()->json(["message" => $seat], 200);
        if (!$seat) {
            return response()->json(["message" => "Ghế đã được đặt, vui lòng reload lại trang web để được cập nhật"], 400);
        }

        $customer_id = $data['customer_id'];
        $discount = $data['discount'];
        $price = $data['price'];
        $quantity = $data['quantity'];
        $customer = Customer::find($customer_id);
        if (!$customer) {
            return response()->json(["message" => "Khách hàng không tồn tại"], 400);
        }

        $vnpay =  config("vnpay");

        $vnp_TmnCode = $vnpay['vnp_TmnCode'];
        $vnp_HashSecret = $vnpay['vnp_HashSecret'];
        $vnp_Url = $vnpay['vnp_Url'];
        $vnp_Returnurl = $vnpay['vnp_Returnurl'] .
            "?customer_id=$customer_id&discount=$discount&price=$price&quantity=$quantity
            &seat=$seat";

        $vnp_TxnRef = rand(1, 10000); //Mã giao dịch thanh toán tham chiếu của merchant";
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+10 minutes', strtotime($startTime)));

        $vnp_TxnRef = rand(1, 10000); //Mã giao dịch thanh toán tham chiếu của merchant
        $vnp_Amount = $data['amount']; // Số tiền thanh toán
        $vnp_Locale = $data['language']; //Ngôn ngữ chuyển hướng thanh toán
        $vnp_BankCode = $data['bankCode']; //Mã phương thức thanh toán
        // $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán
        $vnp_IpAddr = $request->ip(); //IP Khách hàng thanh toán

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => "Thanh toan GD:" . $vnp_TxnRef,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expire
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json($vnp_Url, 200);
    }



    private function createTicket($temporaryTicket, $invoice_id)
    {
        $trip_id = $temporaryTicket->trip_id;
        $customer_id = $temporaryTicket->customer_id;
        $trip = Trip::with(['route.start_address', 'route.end_address', 'bus'])->find($trip_id);
        $customer = Customer::find($customer_id);

        $ticket_id = mt_rand(10000000, 99999999);
        $route = $trip->route;
        $bus = $trip->bus;
        $first_name = $customer->first_name;
        $last_name = $customer->last_name;
        $phone_number = $customer->phone_number;
        $email = $customer->email;
        $route_name = $route->name;
        $date = $trip->date;
        $start_time = $trip->start_time;
        $end_time = $trip->end_time;
        $start_address = BusStation::find($route->start_address)->address;
        $end_address = BusStation::find($route->end_address)->address;
        $price = $trip->price;
        $license = $bus->license;
        $seat = $temporaryTicket->seat;

        $ticket = [
            "id" => Uuid::uuid4(),
            "ticket_id" => $ticket_id,
            "trip_id" => $trip_id,
            "customer_id" => $customer_id,
            "invoice_id" => $invoice_id,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "phone_number" => $phone_number,
            "email" => $email,
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

        // print_r($ticket);
        Ticket::create($ticket);

        return $ticket['ticket_id'];
    }


    private function handleTicket($data, $invoice_id)
    {
        $seats = explode(',', $data);
        $res = [];
        foreach ($seats as $seat) {
            $temporaryTicket = TemporaryTicket::find($seat);
            $ticket_id = $this->createTicket($temporaryTicket, $invoice_id);
            array_push($res, $ticket_id);
            $temporaryTicket->delete();
        }
        return join(",", $res);
    }


    private function deleteTemporaryTicket($seats)
    {
        try {
            $seats = explode(',', $seats);
            foreach ($seats as $seat) {
                $temporaryTicket = TemporaryTicket::find($seat);
                $temporaryTicket->delete();
            }
        } catch (\Throwable $th) {
        }
    }

    public function get(Request $request)
    {
        $ip = $request->ip();
        if ($ip != '127.0.0.1') {
            return redirect(env("REACT_URL", "http://localhost:3000/") . "ket-qua-dat-ve?status=failure");
        }

        $customer_id = $request->input("customer_id");
        $customer = Customer::find($customer_id);
        if (!$customer) {
            return redirect(env("REACT_URL", "http://localhost:3000/") . "ket-qua-dat-ve?status=failure");
        }

        $transaction = [];
        $transaction['vnp_TransactionStatus'] = $request->input('vnp_TransactionStatus');
        $status = $transaction['vnp_TransactionStatus'];

        // handle delete veTam
        if ($status != '00') {
            $this->deleteTemporaryTicket($request->input("seat"));
            return redirect(env("REACT_URL", "http://localhost:3000/") . "ket-qua-dat-ve?status=failure");
        }

        $transaction['vnp_Amount'] = $request->input('vnp_Amount');
        $transaction['vnp_BankCode'] = $request->input('vnp_BankCode');
        $transaction['vnp_CardType'] = $request->input('vnp_CardType');
        $transaction['vnp_OrderInfo'] = $request->input('vnp_OrderInfo');
        $transaction['vnp_PayDate'] = $request->input('vnp_PayDate');
        $transaction['vnp_ResponseCode'] = $request->input('vnp_ResponseCode');
        $transaction['vnp_TmnCode'] = $request->input('vnp_TmnCode');
        $transaction['vnp_TransactionNo'] = $request->input('vnp_TransactionNo');
        $transaction['vnp_TxnRef'] = $request->input('vnp_TxnRef');
        $transaction['id'] = Uuid::uuid4();

        Transaction::create($transaction);

        // solve HoaDon
        $invoice = [];
        $invoice['id'] = Uuid::uuid4();
        $invoice['customer_id'] = $customer_id;
        $invoice['transaction_id'] = $transaction['id'];
        $invoice['phone_number'] = $customer->phone_number;
        $invoice['email'] = $customer->email;
        $invoice['first_name'] = $customer->first_name;
        $invoice['last_name'] = $customer->last_name;
        $invoice['discount'] = $request->input('discount');
        $invoice['price'] = $request->input('price');
        $invoice['quantity'] = $request->input('quantity');
        $invoice['total_price'] = $transaction['vnp_Amount'];

        Invoice::create($invoice);


        // solve temporary tickets
        $tickets =  $this->handleTicket($request->input('seat'), $invoice['id']);

        SendEmailJob::dispatch('ResultTicket', $customer->email, $tickets);

        // Mail::to($customer->email)->send(new ResultTicket($tickets));
        return redirect(env("REACT_URL", "http://localhost:3000/") . "ket-qua-dat-ve?status=success");
    }
}
