<?php

namespace App\Models;

use App\Core\Model;

class Doc extends Model
{
    protected static $table = 'docs';
    protected static $primaryKey = 'id';
    protected array $hidden = [];
    
    protected static array $searchable = [];
}
