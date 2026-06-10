<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use SoftDeletes;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'created_by',
        'deleted_by',
    ];

    public $timestamps = false;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id')
            ->using(PermissionRole::class)
            ->withPivot(['created_at', 'created_by', 'deleted_at', 'deleted_by']);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')
            ->using(RoleUser::class)
            ->withPivot(['created_at', 'created_by', 'deleted_at', 'deleted_by']);
    }
}
