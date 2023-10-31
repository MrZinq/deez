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
        Schema::create('offer_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status')->default('0');
            $table->integer('offer_id')->unsigned();
            $table->foreign('offer_id')->references('id')->on('offers');
            $table->integer('listing_id')->unsigned();
            $table->foreign('listing_id')->references('id')->on('listings');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('user_is')->nullable();
            $table->text('reason')->nullable();
            $table->integer('user_staff')->nullable()->unsigned();
            $table->foreign('user_staff')->references('id')->on('users');
            $table->timestamp('closed_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('offer_reports');
    }
};
