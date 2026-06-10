<?php

namespace App\DTO;

use Illuminate\Http\Request;

readonly class PermissionCollectionDTO
{
    public array $data;
    public array $meta;

    public function __construct(Request|array|null $data = null, array $meta = [])
    {
        if ($data instanceof Request) {
            $arr = $data->input('permissions', []);
        } else {
            $arr = is_array($data) ? ($data['permissions'] ?? $data) : [];
        }

        $this->data = array_map(fn($p) => new PermissionDTO($p), $arr);
        $this->meta = $meta;
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(fn(PermissionDTO $p) => $p->toArray(), $this->data),
            'meta' => $this->meta,
        ];
    }
}
