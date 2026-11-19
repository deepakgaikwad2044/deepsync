<?php

namespace App\Models;

use App\Core\Model;

class RoleUser extends Model
{
    protected static $table = 'role_user';
    protected static $primaryKey = 'id';
    protected array $hidden = [];

    protected static array $searchable = [];
}
