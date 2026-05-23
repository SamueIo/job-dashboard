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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
    
            $table->string('gmail_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    
            $table->string('subject')->nullable();
            $table->string('from')->nullable();
            $table->text('snippet')->nullable();
            $table->longText('body')->nullable();
    
            $table->string('status')->nullable(); 
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
