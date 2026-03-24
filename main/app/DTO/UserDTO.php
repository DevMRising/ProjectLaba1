<?php

namespace App\DTO;

use Illuminate\Http\Request;

class UserDTO {
    public string $id;
    public string $username;
    public string $email;
    public string $password;
    public string $c_password;
    public string $birthday;
    public function __construct(Request $request)
    {
        
    }
};

?>