<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('end_of_lives', function (Blueprint $table) {
            $table->id();

            // 1 ligne par produit (Volet 9 unique)
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete()
                ->unique();

            // Champs généraux
            $table->boolean('is_recoverable');
            $table->text('comment')->nullable();

            $table->date('end_of_life_date');
            $table->foreignId('end_of_life_country_id')->constrained('countries');

            // Options (Oui/Non obligatoires)
            $table->boolean('reuse');
            $table->boolean('recycling');
            $table->boolean('incineration');
            $table->boolean('composting');
            $table->boolean('landfill');

            // Détails si RECYCLING = true
            $table->foreignId('recycling_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('recycling_method')->nullable();
            $table->string('recycling_valued_product')->nullable();
            $table->string('recycling_organization')->nullable();

            // Détails si INCINERATION = true
            $table->foreignId('incineration_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('incineration_method')->nullable();
            $table->string('incineration_valued_product')->nullable();
            $table->string('incineration_organization')->nullable();

            // Détails si COMPOSTING = true
            $table->foreignId('composting_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('composting_method')->nullable();
            $table->string('composting_valued_product')->nullable();
            $table->string('composting_organization')->nullable();

            // Détails si LANDFILL = true
            $table->foreignId('landfill_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('landfill_method')->nullable();
            $table->string('landfill_valued_product')->nullable();
            $table->string('landfill_organization')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('end_of_lives');
    }
};
