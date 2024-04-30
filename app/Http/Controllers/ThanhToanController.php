<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThanhToanController extends Controller
{
    public function post(Request $request)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $data = $request->all();
        $vnpay =  config("vnpay");

        $vnp_TmnCode = $vnpay['vnp_TmnCode'];
        $vnp_HashSecret = $vnpay['vnp_HashSecret'];
        $vnp_Url = $vnpay['vnp_Url'];
        $vnp_Returnurl = $vnpay['vnp_Returnurl'];
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

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
        $data = [];
        $data['vnp_Amount'] = $request->input('vnp_Amount');
        $data['vnp_BankCode'] = $request->input('vnp_BankCode');
        $data['vnp_CardType'] = $request->input('vnp_CardType');
        $data['vnp_OrderInfo'] = $request->input('vnp_OrderInfo');
        $data['vnp_PayDate'] = $request->input('vnp_PayDate');
        $data['vnp_ResponseCode'] = $request->input('vnp_ResponseCode');
        $data['vnp_TmnCode'] = $request->input('vnp_TmnCode');
        $data['vnp_TransactionNo'] = $request->input('vnp_TransactionNo');
        $data['vnp_TransactionStatus'] = $request->input('vnp_TransactionStatus');
        $data['vnp_TxnRef'] = $request->input('vnp_TxnRef');


        return $ip;
    }
}
