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
        Schema::table('emails', function (Blueprint $table) {
            $table->string('company')->nullable();
            $table->string('role')->nullable();
            $table->string('stage')->nullable();
            $table->timestamp('interview_at')->nullable();
            $table->string('meeting_link')->nullable();
            $table->float('confidence')->nullable();
            $table->text('summary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            //
        });
    }
};
