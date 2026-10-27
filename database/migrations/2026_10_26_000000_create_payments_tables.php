<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount_mad');
            $table->string('status', 24)->default('awaiting_transfer');
            $table->timestamp('transfer_reported_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('path')->unique();
            $table->string('mime_type', 80);
            $table->unsignedInteger('size_bytes');
            $table->string('status', 24)->default('submitted');
            $table->text('review_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('administrator_id')->constrained('users')->restrictOnDelete();
            $table->string('action', 24);
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        $rates = config('rooms.rates_mad');

        foreach (DB::table('reservations')->get(['id', 'room_type', 'check_in', 'check_out']) as $reservation) {
            $rate = $rates[$reservation->room_type] ?? null;

            if (! is_int($rate) || $rate < 1) {
                throw new RuntimeException("Missing valid configured room rate for [{$reservation->room_type}].");
            }

            $nights = CarbonImmutable::parse($reservation->check_in)
                ->diffInDays(CarbonImmutable::parse($reservation->check_out));

            if ($nights < 1) {
                throw new RuntimeException("Reservation [{$reservation->id}] has invalid stay dates.");
            }

            DB::table('payments')->insert([
                'reservation_id' => $reservation->id,
                'amount_mad' => $rate * $nights,
                'status' => 'awaiting_transfer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_decisions');
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('payments');
    }
};
