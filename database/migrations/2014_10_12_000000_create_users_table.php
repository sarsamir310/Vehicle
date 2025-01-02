<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('Full_name');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->string('phone_number')->nullable();
            $table->string('driving_license')->nullable(); // Field to store driving license image
            $table->string('job_title')->nullable(); // Field for job title
            $table->string('location')->nullable(); // Field for location
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('api_token', 60)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
