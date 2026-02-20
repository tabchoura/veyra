<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yarn_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ex: Air-jet spinning for knitting, carded yarn
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yarn_types');
    }
};
