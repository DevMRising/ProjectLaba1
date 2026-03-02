<?php

namespace App\Http\Controllers;

use App\DTO\ClientInfoDTO;
use App\DTO\DatabaseInfoDTO;
use App\DTO\ServerInfoDTO;
use Illuminate\Http\Request;


class InfoController extends Controller
{
    public function client(Request $request) {
        return response()->json(new ClientInfoDTO($request));
    }

    public function database() {
        return response()->json(new DatabaseInfoDTO());
    }
    
    public function server() {
        return response()->json(new ServerInfoDTO());
    }
}
