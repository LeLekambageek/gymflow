<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('speciality')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('coach_id')->nullable()->constrained()->onDelete('set null');
            $table->string('category');
            $table->integer('capacity');
            $table->integer('duration_minutes');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('room')->nullable();
            $table->enum('status', ['active', 'cancelled', 'full'])->default('active');
            $table->timestamps();
        });

        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('registered_count')->default(0);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_session_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['confirmed', 'cancelled', 'attended', 'no_show'])->default('confirmed');
            $table->timestamp('booked_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('type'); // subscription, course, other
            $table->string('method'); // cash, card, transfer, mobile
            $table->enum('status', ['paid', 'pending', 'refunded', 'failed'])->default('paid');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('course_sessions');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('coaches');
    }
};
