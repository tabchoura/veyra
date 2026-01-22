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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Cotton", "Acrylic fiber"
            $table->string('code')->nullable(); // Ex: "CO", "PAC", "AR"
            $table->string('region')->default('Global'); // Ex: "Global", "China", "India"
            $table->timestamps();
            
            // Index pour recherche rapide
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};