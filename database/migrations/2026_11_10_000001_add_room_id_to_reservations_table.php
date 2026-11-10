<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('room_id')
                ->nullable()
                ->after('room_type')
                ->constrained('rooms')
                ->restrictOnDelete();
            $table->index(['room_id', 'status', 'check_in', 'check_out']);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['room_id', 'status', 'check_in', 'check_out']);
            $table->dropConstrainedForeignId('room_id');
        });
    }
};
