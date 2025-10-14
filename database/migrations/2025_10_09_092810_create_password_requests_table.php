<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_requests', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index(); // Email pengguna
            $table->integer('role');          // 2: Guru, 3: Siswa
            $table->string('requested_password')->nullable(); // Password yang diinginkan oleh user
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_requests');
    }
};