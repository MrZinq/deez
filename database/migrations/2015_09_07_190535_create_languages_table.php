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
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('app_name', 100);
            $table->string('flag', 100)->nullable();
            $table->string('abbr', 3);
            $table->tinyInteger('active')->unsigned()->default('1');
            $table->tinyInteger('default')->unsigned()->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('languages');
    }
};
