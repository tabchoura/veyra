<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Producer info
            $table->string('producing_organization')->nullable(); // non obligatoire
            $table->foreignId('producing_country_id')->constrained('countries'); // obligatoire
            $table->string('address')->nullable(); // non obligatoire
            $table->string('postal_code')->nullable(); // non obligatoire (texte)

            // Type
            $table->foreignId('accessory_type_id')->constrained('accessory_types'); // obligatoire
            $table->string('accessory_type_other')->nullable(); // obligatoire si type=Other (validation API)

            // Weight
            $table->decimal('weight', 10, 3)->unsigned(); // obligatoire

            // Date
            $table->date('production_date')->nullable(); // non obligatoire

            // Certification (si oui -> champs obligatoires en validation)
            $table->boolean('has_certification')->default(false); // Oui/Non :contentReference[oaicite:3]{index=3}
            $table->string('certificate_number')->nullable();     // obligatoire si certif=Oui :contentReference[oaicite:4]{index=4}
            $table->date('validity_date')->nullable();            // obligatoire si certif=Oui :contentReference[oaicite:5]{index=5}
            $table->string('transaction_reference')->nullable();  // si certif=Oui (mais “non obligatoire” globalement) :contentReference[oaicite:6]{index=6}

            // Client audit (si oui -> commentaire obligatoire)
            $table->boolean('has_client_audit')->default(false); // :contentReference[oaicite:7]{index=7}
            $table->text('audit_comments')->nullable();          // obligatoire si audit=Oui :contentReference[oaicite:8]{index=8}

            // Environment (obligatoires 0-100)
            $table->decimal('renewable_energy_percentage', 5, 2)->unsigned()->default(0); // :contentReference[oaicite:9]{index=9}
            $table->decimal('recycled_water_percentage', 5, 2)->unsigned()->default(0);   // :contentReference[oaicite:10]{index=10}

            $table->timestamps();

            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
