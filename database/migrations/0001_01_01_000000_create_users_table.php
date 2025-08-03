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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('username');
            $table->text('fcm_token')->nullable();
            $table->string('phone_number');
            $table->foreignId('last_win_draw_id')
                ->constrained('draws')
            ->nullOnDelete();
            $table->string('access_token')->unique();
            $table->timestamps();
        });


        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamp('login_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'))
                ->index();

            $table->unique(['user_id', DB::raw('DATE(login_at)')], 'user_session_per_day_unique');
        });

        Schema::table('draws', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->decimal('total_amount')->default(0);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::table('users_draws', function (Blueprint $table) {
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount')->default(0);
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('draws');
        Schema::dropIfExists('users_draws');

    }
};
