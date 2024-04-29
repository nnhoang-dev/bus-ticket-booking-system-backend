<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\NhanVien;

class ChuyenXeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $permission = ['QL', 'VH'];
        $accessID = $request->header('accessID');
        $nhanVien = NhanVien::find($accessID);
        if ((!$nhanVien) || ($nhanVien['status'] == 0)) {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
        }

        if (in_array($nhanVien['role'], $permission)) {
            return $next($request);
        }
        return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
    }
}