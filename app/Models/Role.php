<?php

namespace App\Models;

use App\Core\Model;

class Role extends Model
{
    protected static $table = 'roles';
    protected static $primaryKey = 'id';
    protected array $hidden = [];

    protected static array $searchable = [];
}
