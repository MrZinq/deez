<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('backport_role_menu', function (Blueprint $table) {
            $config = config('laravel-permission.table_names');

            $table->integer('role_id')->unsigned();
            $table->integer('menu_id')->unsigned();
            $table->timestamps();

            $table->foreign('role_id')
                ->references('id')
                ->on($config['roles'])
                ->onDelete('cascade');

            $table->foreign('menu_id')
                ->references('id')
                ->on('backport_menu')
                ->onDelete('cascade');

            $table->primary(['role_id', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('backport_role_menu');
    }
};
