<?php
namespace App\Core;

use App\Core\Model;

class PasswordReset extends Model
{
  protected static $table = "password_resets";
  protected static $primaryKey = "email"; // optional, just for clarity
}
