<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\KhachHang;
use App\Models\NhanVien;

class KhachHangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $accessID = $request->header('accessID');
        $nhanVien = NhanVien::find($accessID);
        $khachHang = KhachHang::find($accessID);

        if (isset($khachHang)) {
            $method = $request->method();
            if (($method == 'GET' || $method == 'PUT' || $method == 'PATCH')
                && ($request->route('khach_hang') == $khachHang['id'])
            ) {
                return $next($request);
            }
        } elseif (isset($nhanVien)) {
            $role = $nhanVien['nv_role'];
            if ($role == 'QL' || $role == 'CS') {
                return $next($request);
            }
        }
        return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
    }
}
