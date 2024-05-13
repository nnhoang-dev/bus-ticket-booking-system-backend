<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

// class EmployeeController extends Controller
// {
//     public function index()
//     {
//         try {
//             $nhanViens = Employee::all();
//             return EmployeeResource::collection($nhanViens);
//         } catch (\Throwable $th) {
//             return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
//         }
//     }



//     public function show(string $id)
//     {
//         try {
//             $nhanVien = Employee::find($id);
//             if (!$nhanVien) {
//                 return response()->json(['message' => 'Không tồn tại nhân viên'], 404);
//             }
//             return new EmployeeResource($nhanVien);
//         } catch (\Throwable $th) {
//             return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
//         }
//     }




//     public function update(Request $request, string $id)
//     {
//         try {
//             if ($request->isMethod('PUT')) {
//                 // validaion
//                 $validator = Validator::make($request->all(), [
//                     'first_name' => 'required|string|max:255',
//                     'last_name' => 'required|string|max:255',
//                     'date_of_birth' => 'required|date',
//                     'gender' => 'required|in:0,1',
//                     'address' => 'required|string',
//                     'role' => 'required|in:QL,VH,CS,KT,TX',
//                     'status' => 'in:0,1',
//                 ]);
//             } else if ($request->isMethod('PATCH')) {
//                 // validaion
//                 $validator = Validator::make($request->all(), [
//                     'phone_number' => 'string|unique:nhan_vien,phone_number',
//                     'password' => 'string|min:6',
//                     'email' => 'email|unique:nhan_vien,email',
//                     'status' => 'in:0,1',

//                 ]);
//             }
//             if ($validator->stopOnFirstFailure()->fails()) {

//                 $errors = $validator->errors();
//                 foreach ($errors->all() as $error) {
//                     return response()->json(["message" => $error], 400);
//                 }
//             }


//             $nhanVien = Employee::find($id);
//             if (!$nhanVien) {
//                 return response()->json(['message' => 'Không tồn tại nhân viên'], 404);
//             }

//             $data = $request->all();
//             if (isset($data['password'])) {
//                 $data['password'] = Hash::make($data['password']);
//             }


//             $nhanVien->update($data);
//             return response()->json(['message' => 'Cập nhật nhân viên thành công'], 200);
//         } catch (\Throwable $th) {
//             return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
//         }
//     }


//     public function destroy(string $id)
//     {
//         try {
//             $nhanVien = Employee::find($id);
//             if (!$nhanVien) {
//                 return response()->json(['message' => 'Không tồn tại nhân viên'], 404);
//             }

//             $nhanVien->delete();
//             return response()->json(['message' => 'Xóa nhân viên thành công'], 200);
//         } catch (\Throwable $th) {
//             return response()->json(['message' => 'Lỗi ở phía server', "exception" => $th], 500);
//         }
//     }
// }
