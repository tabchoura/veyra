<?php

// database/migrations/2024_01_01_000006_create_yarns_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yarns', function (Blueprint $table) {
            $table->id();
            
            // Liaison avec le produit (Relation One-to-Many)
            $table->foreignId('product_id')
                  ->constrained()
                  ->onDelete('cascade');

            // --- Identification & Localisation  ---
            $table->string('producing_organization')->nullable(); // "Organisme producteur"
            $table->foreignId('producing_country_id')             // "Pays producteur"
                  ->constrained('countries');
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();

            // --- Caractéristiques du Fil  ---
            $table->string('yarn_type')->nullable();    // "Type de fil" (ex: Ring Spun)
            $table->decimal('percentage', 5, 2);        // "%" (Somme doit faire 100%)
            $table->date('production_date')->nullable();

            // --- Certification (Conditionnel)  ---
            $table->boolean('has_certification')->default(false);
            $table->string('certificate_number')->nullable(); // Obligatoire si has_certification = true
            $table->date('validity_date')->nullable();        // Obligatoire si has_certification = true
            $table->string('transaction_reference')->nullable();

            // --- Audit Client (Conditionnel)  ---
            $table->boolean('has_client_audit')->default(false);
            $table->text('audit_comments')->nullable();       // Obligatoire si has_client_audit = true

            // --- Impact Environnemental (Spécifique Volet 4)  ---
            $table->decimal('renewable_energy_percent', 5, 2); // "Énergie renouvelable %"
            $table->decimal('recycled_water_percent', 5, 2);   // "Eau recyclée %"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yarns');
    }
};