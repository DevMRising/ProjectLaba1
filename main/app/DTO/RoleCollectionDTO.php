<?php

namespace App\DTO;

use Illuminate\Http\Request;

readonly class RoleCollectionDTO
{
    public array $data;
    public array $meta;

    public function __construct(Request|array|null $data = null, array $meta = [])
    {
        if ($data instanceof Request) {
            $arr = $data->input('roles', []);
        } else {
            $arr = is_array($data) ? ($data['roles'] ?? $data) : [];
        }

        $this->data = array_map(fn($r) => new RoleDTO($r), $arr);
        $this->meta = $meta;
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn(RoleDTO $r) => $r->toArray(), $this->data),
            'meta' => $this->meta,
        ];
    }
}
