<?php

namespace App\DTO;

use Illuminate\Http\Request;
use App\DTO\RoleDTO;

readonly class UserDTO
{
    public ?string $id;
    public ?string $username;
    public ?string $email;
    public ?string $password;
    public ?string $c_password;
    public ?string $birthday;
    public ?array $roles;

    public function __construct(Request|array|null $data = null)
    {
        if ($data instanceof Request) {
            $this->id = $data->input('id', null);
            $this->username = $data->input('username', null);
            $this->email = $data->input('email', null);
            $this->password = $data->input('password', null);
            $this->c_password = $data->input('c_password', null);
            $this->birthday = $data->input('birthday', null);
            $roles = $data->input('roles', null);
            if (is_array($roles)) {
                $this->roles = array_map(fn($r) => new RoleDTO($r), $roles);
            } else {
                $this->roles = null;
            }
            return;
        }

        $arr = is_array($data) ? $data : [];
        $this->id = $arr['id'] ?? null;
        $this->username = $arr['username'] ?? null;
        $this->email = $arr['email'] ?? null;
        $this->password = $arr['password'] ?? null;
        $this->c_password = $arr['c_password'] ?? null;
        $this->birthday = $arr['birthday'] ?? null;
        if (!empty($arr['roles']) && is_array($arr['roles'])) {
            $this->roles = array_map(fn($r) => new RoleDTO($r), $arr['roles']);
        } else {
            $this->roles = null;
        }
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
            'roles' => $this->roles ? array_map(fn(RoleDTO $r) => $r->toArray(), $this->roles) : null,
        ];
    }
}

?>