<?php

namespace App\Models;

use App\Core\Model;

class Permission extends Model
{
    protected static $table = 'permissions';
    protected static $primaryKey = 'id';
    protected array $hidden = [];

    protected static array $searchable = [];
}
