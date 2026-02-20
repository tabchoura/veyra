<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('passport_user_access', function (Blueprint $table) {
            $table->id();

            $table->foreignId('passport_id')->constrained('passports')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['passport_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passport_user_access');
    }
};
