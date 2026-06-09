<?php

namespace App\DTO;

use Illuminate\Http\Request;

readonly class UserDTO
{
    public ?string $id;
    public ?string $username;
    public ?string $email;
    public ?string $password;
    public ?string $c_password;
    public ?string $birthday;

    public function __construct(Request|array|null $data = null)
    {
        if ($data instanceof Request) {
            $this->id = $data->input('id', null);
            $this->username = $data->input('username', null);
            $this->email = $data->input('email', null);
            $this->password = $data->input('password', null);
            $this->c_password = $data->input('c_password', null);
            $this->birthday = $data->input('birthday', null);
            return;
        }

        $arr = is_array($data) ? $data : [];
        $this->id = $arr['id'] ?? null;
        $this->username = $arr['username'] ?? null;
        $this->email = $arr['email'] ?? null;
        $this->password = $arr['password'] ?? null;
        $this->c_password = $arr['c_password'] ?? null;
        $this->birthday = $arr['birthday'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'c_password' => $this->c_password,
            'birthday' => $this->birthday,
        ];
    }
}

?>