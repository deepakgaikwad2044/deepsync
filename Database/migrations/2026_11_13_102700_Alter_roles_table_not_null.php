<?php

use Database\Migration;

class Alter_roles_table_not_null extends Migration
{
    public function up()
    {
        self::$pdo->exec("
            ALTER TABLE roles
            MODIFY name VARCHAR(100) NOT NULL,
            MODIFY slug VARCHAR(100) NOT NULL
        ");
    }

    public function down()
    {
        self::$pdo->exec("
            ALTER TABLE roles
            MODIFY name VARCHAR(100) NULL,
            MODIFY slug VARCHAR(100) NULL
        ");
    }
}
