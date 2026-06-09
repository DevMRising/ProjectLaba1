<?php

namespace App\DTO;

readonly class AuthSuccessDTO
{
    public array $user;
    public string $access_token;
    public string $refresh_token;
    public int $expires_in;

    public function __construct(array $user = [], string $access = '', string $refresh = '', int $expires = 0)
    {
        $this->user = $user;
        $this->access_token = $access;
        $this->refresh_token = $refresh;
        $this->expires_in = $expires;
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expires_in' => $this->expires_in,
        ];
    }
}
