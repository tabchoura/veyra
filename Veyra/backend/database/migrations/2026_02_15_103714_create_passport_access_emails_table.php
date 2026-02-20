<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('passport_access_emails', function (Blueprint $table) {
            $table->id();

            $table->foreignId('passport_id')->constrained('passports')->cascadeOnDelete();
            $table->string('email');

            $table->timestamps();

            $table->unique(['passport_id', 'email']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passport_access_emails');
    }
};
