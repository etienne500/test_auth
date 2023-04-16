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
        Schema::create('logs', function (Blueprint $table) {
            // $table->id();
            // $table->morphs('loggable');
            // $table->string('message');
            // $table->timestamps();
            
            $table->id();
            $table->string('loggable_id');
            $table->string('loggable_type');
            $table->index(['loggable_id', 'loggable_type']);
            $table->string('message');
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
