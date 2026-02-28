<?php

namespace App\DTO;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientInfoDTO {
    public string $ip;
    public string $user_agent;

    public function __construct(Request $request)
    {
        $this->ip = $request->ip();
        $this->user_agent = $request->header('User_Agent');
    }
};

?>