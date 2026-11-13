<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_availability_locks', function (Blueprint $table) {
            $table->string('room_type', 20)->primary();
            $table->unsignedBigInteger('lock_version')->default(0);
        });

        DB::table('room_availability_locks')->insert([
            ['room_type' => 'single', 'lock_version' => 0],
            ['room_type' => 'double', 'lock_version' => 0],
            ['room_type' => 'triple', 'lock_version' => 0],
            ['room_type' => 'suite', 'lock_version' => 0],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('room_availability_locks');
    }
};
