<?php

namespace App\Http\Controllers;

use App\DTO\UserDTO;
use App\DTO\AuthSuccessDTO;
use App\DTO\TokenListDTO;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function client(Request $request) {
        return response()->json(new UserDTO($request));
    }

    public function database() {
        
    }
    
    public function server() {
        
    }
}
