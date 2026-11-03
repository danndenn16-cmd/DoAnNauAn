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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');                  // chủ cuộc trò chuyện (thành viên)
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');         // người gửi tin này
            $table->text('body');
            $table->timestamp('read_at')->nullable();                                          // thời điểm bên kia đã đọc
            $table->timestamps();
            $table->index(['user_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
