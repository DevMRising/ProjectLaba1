<?php

namespace App\DTO;

readonly class TokenListDTO
{
    public array $tokens;

    public function __construct(array $tokens = [])
    {
        $this->tokens = $tokens;
    }

    public function toArray(): array
    {
        return ['tokens' => $this->tokens];
    }
}
