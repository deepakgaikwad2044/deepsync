<?php

use Database\Migration;
use Database\Schema;

class Create_permission_role_table extends Migration
{
    public function up()
    {
        Schema::create('permission_role', function($table) {
            $table->id();

            // Match permissions.id: unsigned INT
            $table->int('permission_id', true)->notNull();

            // Match roles.id: unsigned INT
            $table->int('role_id', true)->notNull();

            $table->timestamps();

            $table->foreign('permission_id')
                  ->references('id')
                  ->on('permissions')
                  ->onDelete('cascade')
                  ->name('fk_permission_role_permission');

            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade')
                  ->name('fk_permission_role_role');
        });

        self::$pdo->exec("
            ALTER TABLE permission_role
            ADD CONSTRAINT uq_permission_role
            UNIQUE (permission_id, role_id)
        ");
    }

    public function down()
    {
        Schema::dropIfExists('permission_role');
    }
}
