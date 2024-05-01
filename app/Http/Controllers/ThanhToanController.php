<?php

namespace App\Http\Controllers;

use App\Models\ChuyenXe;
use App\Models\GiaoDich;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\NhaXe;
use App\Models\TuyenXe;
use App\Models\VeTam;
use App\Models\VeXe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class ThanhToanController extends Controller
{
    private function checkSeat($seat, $chuyenXe)
    {
        $seatRequest = explode(",", $seat);
        $seatChuyenXe = explode(",", $chuyenXe['seat']);

        foreach ($seatRequest as $seat) {
            if ((!is_numeric($seat)) || (intval($seat) < 1) || (intval($seat) > 34)) {
                return false;
            }
        }

        foreach ($seatRequest as $seat) {
            if (in_array($seat, $seatChuyenXe)) {
                return false;
            }
        }
        return true;
    }


    private function createVeTam($data, $chuyenXe)
    {
        $seats = explode(",", $data['seat']);
        $res = "";
        foreach ($seats as $seat) {
            $veTam = $data;
            $veTam['seat'] = $seat;
            $veTam['id'] = Uuid::uuid4();
            VeTam::create($veTam);
            $res = $res . $veTam['id'] . ",";
        }
        $res = substr($res, 0, -1);

        $seat = $chuyenXe['seat'];
        if ($seat == "") {
            $seat = $seat . $data['seat'];
        } else {
            $seat = $seat . "," . $data['seat'];
        }

        $chuyenXe->update([
            "seat" => $seat,
        ]);

        return $res;
    }


    private function order($request)
    {
        try {
            $validator = Validator::make($request, [
                'chuyen_xe_id' => 'required|string|exists:chuyen_xe,id',
                'khach_hang_id' => 'required|string|exists:khach_hang,id',
                'seat' => 'required',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return null;
            }

            $data = $request;
            $chuyenXe = ChuyenXe::find($data['chuyen_xe_id']);

            if ($this->checkSeat($data['seat'], $chuyenXe) == false) {
                return null;
            }

            $res = $this->createVeTam($data, $chuyenXe);
            return $res;
        } catch (\Throwable $th) {
            return null;
        }
    }



    public function handleVeXe($data, $hoa_don_id)
    {
        $seats = explode(',', $data);
        $res = [];
        foreach ($seats as $seat) {

            $veTam = VeTam::find($seat);
            $chuyen_xe_id = $veTam->chuyen_xe_id;
            $khach_hang_id = $veTam->khach_hang_id;
            $chuyenXe = ChuyenXe::with(['tuyen_xe.start_address', 'tuyen_xe.end_address', 'xe'])->find($chuyen_xe_id);
            $khachHang = KhachHang::find($khach_hang_id);

            $tuyenXe = $chuyenXe->tuyen_xe;
            $xe = $chuyenXe->xe;
            $first_name = $khachHang->first_name;
            $last_name = $khachHang->last_name;
            $phone_number = $khachHang->phone_number;
            $route_name = $tuyenXe->name;
            $date = $chuyenXe->date;
            $start_time = $chuyenXe->start_time;
            $end_time = $chuyenXe->end_time;
            $start_address = NhaXe::find($tuyenXe->start_address)->address;
            $end_address = NhaXe::find($tuyenXe->end_address)->address;
            $price = $chuyenXe->price;
            $license = $xe->license;
            $seat = $veTam->seat;

            $veXe = [
                "id" => Uuid::uuid4(),
                "chuyen_xe_id" => $chuyen_xe_id,
                "khach_hang_id" => $khach_hang_id,
                "hoa_don_id" => $hoa_don_id,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "phone_number" => $phone_number,
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

            VeXe::create($veXe);
            array_push($res, $veXe['id']);
            $veTam->delete();
        }
        return join(",", $res);
    }

    public function post(Request $request)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $data = $request->all();

        $veTam = [
            "chuyen_xe_id" => $data['chuyen_xe_id'],
            "khach_hang_id" => $data['khach_hang_id'],
            "seat" => $data['seat']
        ];

        $seat = $this->order($veTam);
        if (!$seat) {
            return response()->json("http://localhost:3000?status=fail", 400);
        }

        $khach_hang_id = $data['khach_hang_id'];
        $discount = $data['discount'];
        $price = $data['price'];
        $quantity = $data['quantity'];
        $khachHang = KhachHang::find($khach_hang_id);
        if (!$khachHang) {
            return response()->json("http://localhost:3000?status=fail", 400);
        }

        $vnpay =  config("vnpay");

        $vnp_TmnCode = $vnpay['vnp_TmnCode'];
        $vnp_HashSecret = $vnpay['vnp_HashSecret'];
        $vnp_Url = $vnpay['vnp_Url'];
        $vnp_Returnurl = $vnpay['vnp_Returnurl'] .
            "?khach_hang_id=$khach_hang_id&discount=$discount&price=$price&quantity=$quantity
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

    public function get(Request $request)
    {
        $ip = $request->ip();
        if ($ip != '127.0.0.1') {
            return redirect('http://localhost:3000?status=fail');
        }

        $khach_hang_id = $request->input("khach_hang_id");
        $khachHang = KhachHang::find($khach_hang_id);
        if (!$khachHang) {
            return redirect("http://localhost:3000?status=fail");
        }

        $giaoDich = [];
        $giaoDich['vnp_TransactionStatus'] = $request->input('vnp_TransactionStatus');
        $status = $giaoDich['vnp_TransactionStatus'];

        // handle delete veTam
        if ($status != '00') {
            return "fail";
            // return redirect("http://localhost:3000?status=fail");
        }

        $giaoDich['vnp_Amount'] = $request->input('vnp_Amount');
        $giaoDich['vnp_BankCode'] = $request->input('vnp_BankCode');
        $giaoDich['vnp_CardType'] = $request->input('vnp_CardType');
        $giaoDich['vnp_OrderInfo'] = $request->input('vnp_OrderInfo');
        $giaoDich['vnp_PayDate'] = $request->input('vnp_PayDate');
        $giaoDich['vnp_ResponseCode'] = $request->input('vnp_ResponseCode');
        $giaoDich['vnp_TmnCode'] = $request->input('vnp_TmnCode');
        $giaoDich['vnp_TransactionNo'] = $request->input('vnp_TransactionNo');
        $giaoDich['vnp_TxnRef'] = $request->input('vnp_TxnRef');
        $giaoDich['id'] = Uuid::uuid4();

        GiaoDich::create($giaoDich);

        // solve HoaDon
        $hoaDon = [];
        $hoaDon['id'] = Uuid::uuid4();
        $hoaDon['khach_hang_id'] = $khach_hang_id;
        $hoaDon['giao_dich_id'] = $giaoDich['id'];
        $hoaDon['phone_number'] = $khachHang->phone_number;
        $hoaDon['email'] = $khachHang->email;
        $hoaDon['first_name'] = $khachHang->first_name;
        $hoaDon['last_name'] = $khachHang->last_name;
        $hoaDon['discount'] = $request->input('discount');
        $hoaDon['price'] = $request->input('price');
        $hoaDon['quantity'] = $request->input('quantity');
        $hoaDon['total_price'] = $giaoDich['vnp_Amount'];

        HoaDon::create($hoaDon);

        // solve VeTam
        $ve_xe_id =  $this->handleVeXe($request->input('seat'), $hoaDon['id']);
        return redirect("http://localhost:3000?status=success&ve_xe_id=$ve_xe_id");
    }
}
