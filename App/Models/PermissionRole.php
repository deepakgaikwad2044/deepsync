<?php

namespace App\Models;

use App\Core\Model;

class PermissionRole extends Model
{
    protected static $table = 'permission_role';
    protected static $primaryKey = 'id';
    protected array $hidden = [];

    protected static array $searchable = [];
}
