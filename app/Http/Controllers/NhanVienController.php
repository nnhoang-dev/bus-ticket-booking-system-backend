<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhanVien;
use App\Http\Resources\NhanVienResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use function Pest\Laravel\get;

class NhanVienController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $nhanViens = NhanVien::all();
        return NhanVienResource::collection($nhanViens);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|unique:nhan_vien,phone_number',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:nhan_vien,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:0,1',
            'address' => 'required|string',
            'role' => 'required|in:QL,VH,CS,KT,TX',

        ]);
        if ($validator->stopOnFirstFailure()->fails()) {

            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }

        $data = $request->all();
        $data['id'] = Uuid::uuid4()->toString();
        $data['password'] = Hash::make($data['password']);

        NhanVien::create($data);
        return response()->json(['message' => 'Tạo nhân viên thành công'], 201);
    }

    public function show(string $id)
    {
        $nhanVien = NhanVien::find($id);
        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }
        return new NhanVienResource($nhanVien);
    }

    public function update(Request $request, string $id)
    {

        if ($request->isMethod('PUT')) {
            // validaion
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:0,1',
                'address' => 'required|string',
                'role' => 'required|in:QL,VH,CS,KT,TX',
                'status' => 'in:0,1',
            ]);
        } else if ($request->isMethod('PATCH')) {
            // validaion
            $validator = Validator::make($request->all(), [
                'phone_number' => 'string|unique:nhan_vien,phone_number',
                'password' => 'string|min:6',
                'email' => 'email|unique:nhan_vien,email',
                'status' => 'in:0,1',

            ]);
        }
        if ($validator->stopOnFirstFailure()->fails()) {

            $errors = $validator->errors();
            foreach ($errors->all() as $error) {
                return response()->json(["message" => $error], 400);
            }
        }


        // find nhan_vien
        $nhanVien = NhanVien::find($id);
        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        $data = $request->all();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $nhanVien->update($data);
        return response()->json(['message' => 'Cập nhật nhân viên thành công'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $nhanVien = NhanVien::find($id);
        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        $nhanVien->delete();
        return response()->json(['message' => 'Xóa nhân viên thành công'], 200);
    }
}
