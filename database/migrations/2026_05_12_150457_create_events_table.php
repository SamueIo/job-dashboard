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
        Schema::create('events', function (Blueprint $table) {
        
            $table->id();
        
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
        
            $table->foreignId('email_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        
            $table->string('title');
        
            $table->text('description')
                ->nullable();
        
            $table->string('company')
                ->nullable();
        
            $table->timestamp('start_at');
        
            $table->timestamp('end_at')
                ->nullable();
        
            $table->string('type')
                ->default('interview');
        
            $table->string('location')
                ->nullable();
        
            $table->string('meeting_link')
                ->nullable();
        
            $table->boolean('completed')
                ->default(false);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
