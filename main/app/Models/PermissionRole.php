<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionRole extends Pivot
{
    use SoftDeletes;

    protected $table = 'permission_role';

    public $timestamps = false;

    protected $dates = ['created_at', 'deleted_at'];
}
