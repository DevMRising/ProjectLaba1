<?php

namespace App\DTO;

use Illuminate\Http\Request;

readonly class ClientInfoDTO {
    public string $ip;
    public string $userAgent;

    public function __construct(Request $request)
    {
        $this->ip = htmlspecialchars($request->ip());
        $this->userAgent = htmlspecialchars($request->header('User_Agent'));
    }
};

?>