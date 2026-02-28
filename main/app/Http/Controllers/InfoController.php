<?php

namespace App\Http\Controllers;

use App\DTO\ClientInfoDTO;
use Illuminate\Http\Request;


class InfoController extends Controller
{
    public function client(Request $request) {
        return response()->json(new ClientInfoDTO($request));
    }
}
