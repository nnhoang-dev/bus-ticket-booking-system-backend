<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\NhanVien;

class NhanVienMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $permission = ['TX', 'VH', 'CS', 'KT'];
        $accessID = $request->header('accessID');
        $nhanVien = NhanVien::find($accessID);
        if ((!$nhanVien) || ($nhanVien['status'] == 0)) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
        }

        if ($nhanVien['role'] == 'QL') {
            return $next($request);
        } else if (in_array($nhanVien['role'], $permission)) {
            $method = $request->method();
            if (($method == 'GET' || $method == 'PUT' || $method == 'PATCH')
                && ($request->route('nhan_vien') == $accessID)
            ) {
                return $next($request);
            }
        }
        return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
    }
}