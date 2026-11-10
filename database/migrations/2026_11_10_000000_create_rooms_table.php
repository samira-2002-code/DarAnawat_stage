<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number', 40)->unique();
            $table->string('room_type', 20)->index();
            $table->unsignedBigInteger('price_per_night');
            $table->string('status', 20)->default('available')->index();
            $table->timestamps();

            $table->index(['room_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
