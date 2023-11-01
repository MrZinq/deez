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
        Schema::create('backport_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default('0');
            $table->integer('order')->default('0');
            $table->string('title', 50);
            $table->string('icon', 50);
            $table->string('uri', 50)->nullable()->default(null);
            $table->string('permission')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('backport_menu');
    }
};
