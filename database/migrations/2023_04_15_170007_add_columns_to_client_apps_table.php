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
        // Schema::table('client_apps', function (Blueprint $table) {
        //     $table->string('public_key');
        //     $table->string('secret_key');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('client_apps', function (Blueprint $table) {
        //     //
        // });
    }
};
