<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        Schema::create('client_apps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('return_url');
            $table->string('public_key');
            $table->string('secret_key');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
        
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id');
            $table->foreign('client_id')->references('id')->on('client_apps');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_apps');
        Schema::dropIfExists('users');
        Schema::dropIfExists('access_tokens');
    }
};
