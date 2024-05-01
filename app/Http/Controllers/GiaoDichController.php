<?php

namespace App\Http\Controllers;

use App\Models\GiaoDich;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class GiaoDichController extends Controller
{
    public function store($request)
    {
        $request['id'] = Uuid::uuid4();
        GiaoDich::create($request);
    }
}
