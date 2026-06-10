<?php

namespace App\DTO;

use Illuminate\Http\Request;

readonly class PermissionDTO
{
    public ?string $id;
    public ?string $name;
    public ?string $slug;
    public ?string $description;
    public ?string $created_at;
    public ?string $created_by;
    public ?string $deleted_at;
    public ?string $deleted_by;

    public function __construct(Request|array|null $data = null)
    {
        if ($data instanceof Request) {
            $this->id = $data->input('id', null);
            $this->name = $data->input('name', null);
            $this->slug = $data->input('slug', null);
            $this->description = $data->input('description', null);
            $this->created_at = $data->input('created_at', null);
            $this->created_by = $data->input('created_by', null);
            $this->deleted_at = $data->input('deleted_at', null);
            $this->deleted_by = $data->input('deleted_by', null);
            return;
        }

        $arr = is_array($data) ? $data : [];
        $this->id = $arr['id'] ?? null;
        $this->name = $arr['name'] ?? null;
        $this->slug = $arr['slug'] ?? null;
        $this->description = $arr['description'] ?? null;
        $this->created_at = $arr['created_at'] ?? null;
        $this->created_by = $arr['created_by'] ?? null;
        $this->deleted_at = $arr['deleted_at'] ?? null;
        $this->deleted_by = $arr['deleted_by'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'deleted_at' => $this->deleted_at,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
