<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create draws table first (no dependencies)
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // Create users table second (depends on draws)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('username');
            $table->text('fcm_token')->nullable();
            $table->string('phone_number');
            $table->foreignId('last_win_draw_id')
                ->nullable()
                ->constrained('draws', 'id')
                ->nullOnDelete();
            $table->string('access_token')->unique();
            $table->timestamps();
        });

        // Create sessions table third (depends on users)
        Schema::create('sessions', function (Blueprint $table) {
            $table->id()->primary();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->date('login_at')
                ->default(DB::raw('CURRENT_DATE'))
                ->index();

            $table->unique(['user_id', 'login_at'], 'user_session_per_day_unique');
        });

        // Create users_draws table last (depends on sessions, users, and draws)
        Schema::create('users_draws', function (Blueprint $table) {
            $table->id(); // Added primary key
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(0);
            $table->foreignId('draw_id')->constrained('draws')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order of creation
        Schema::dropIfExists('users_draws');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('draws');
    }
};
