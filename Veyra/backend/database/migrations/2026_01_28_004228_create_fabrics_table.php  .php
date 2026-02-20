<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // obligatoire
            $table->foreignId('producing_country_id')->constrained('countries');

            $table->foreignId('fabric_type_id')->constrained('fabric_types');

            // Infos producteur (optionnel)
            $table->string('producing_organization')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 50)->nullable();

            // % tissu
            $table->decimal('percentage', 5, 2)->unsigned();

            // Date (optionnel)
            $table->date('production_date')->nullable();

            // Teinture / finissage (choix depuis la BD)
            $table->boolean('has_dyeing')->default(false);
            $table->foreignId('colouring_method_id')
                ->nullable()
                ->constrained('colouring_methods')
                ->nullOnDelete();

            $table->boolean('has_finishing')->default(false);
            $table->foreignId('finishing_method_id')
                ->nullable()
                ->constrained('finishing_methods')
                ->nullOnDelete();

            // Certification
            $table->boolean('has_certification')->default(false);
            $table->string('certificate_number')->nullable();
            $table->date('validity_date')->nullable();

            // Référence transaction
            $table->string('transaction_reference')->nullable();

            // Audit client
            $table->boolean('has_client_audit')->default(false);
            $table->text('audit_comments')->nullable();

            // Pourcentages impacts
            $table->decimal('renewable_energy_percentage', 5, 2)->unsigned()->default(0);
            $table->decimal('recycled_water_percentage', 5, 2)->unsigned()->default(0);

            // ZDHC
            $table->boolean('zdhc_supply_to_zero')->default(false);
            $table->boolean('zdhc_get_zd')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};
