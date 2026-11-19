<?php

use Database\Migration;
use Database\Schema;

class Create_permissions_table extends Migration
{
    public function up()
    {
        Schema::create('permissions', function($table) {
            $table->id();

            $table->string('name', 100)->notNull();
            $table->unique();

            $table->string('slug', 100)->notNull();
            $table->unique();

            $table->text('description');
            $table->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
