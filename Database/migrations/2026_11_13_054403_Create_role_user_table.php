<?php

use Database\Migration;
use Database\Schema;

class Create_role_user_table extends Migration
{
    public function up()
    {
        Schema::create('role_user', function($table) {
            $table->id();

            // Match users.id: signed INT
            $table->int('user_id')->notNull();

            // Match roles.id: unsigned INT
            $table->int('role_id', true)->notNull();

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->name('fk_role_user_user');

            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade')
                  ->name('fk_role_user_role');
        });

        self::$pdo->exec("
            ALTER TABLE role_user
            ADD CONSTRAINT uq_role_user
            UNIQUE (role_id, user_id)
        ");
    }

    public function down()
    {
        Schema::dropIfExists('role_user');
    }
}
