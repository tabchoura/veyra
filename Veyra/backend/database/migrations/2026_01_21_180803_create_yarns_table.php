<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yarns', function (Blueprint $table) {
            $table->id();

            // Produit lié
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Organisme producteur
            $table->string('producing_organization')->nullable();

            // Pays producteur (obligatoire)
            $table->foreignId('producing_country_id')
                ->constrained('countries')
                ->restrictOnDelete();

            // Adresse / Code postal
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();

            // Type de fil (table Excel "Spinning Methods")
            $table->string('yarn_type')->nullable();

            // Pourcentage
            $table->decimal('percentage', 5, 2)->unsigned();

            // Date de production
            $table->date('production_date')->nullable();

            // Certifications
            $table->boolean('has_certification')->default(false);
            $table->string('certificate_number')->nullable();
            $table->date('validity_date')->nullable();
            $table->string('transaction_reference')->nullable();

            // Audit client
            $table->boolean('has_client_audit')->default(false);
            $table->text('audit_comments')->nullable();

            // Indicateurs environnementaux
            $table->decimal('renewable_energy_percentage', 5, 2)->unsigned();
            $table->decimal('recycled_water_percentage', 5, 2)->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->index('product_id');
            
    $table->foreignId('yarn_type_id')
        ->nullable()
        ->constrained('yarn_types')
        ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yarns');
    }
};
