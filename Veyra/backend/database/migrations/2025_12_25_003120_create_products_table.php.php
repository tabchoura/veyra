<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // =========================
            // VOLET 1 — INITIALISATION DU PRODUIT
            // =========================

            // Code article (texte lecture seul, index automatique) - obligatoire
            $table->string('item_code', 50)->unique();

            // Date / Heure de création (lecture seul, depuis système) - obligatoire
            $table->timestamp('creation_datetime')->useCurrent();

            // Photo du produit (upload) - obligatoire (formats gérés en validation)
            $table->string('product_image', 255);

            // Nom du produit - obligatoire
            $table->string('product_name', 255);

            // Poids (numérique, valeur positive réel) - obligatoire
            $table->decimal('weight', 10, 3)->unsigned();

            // Lot / Série - optionnel
            $table->string('batch_serial', 100)->nullable();

            // Code PRODCOM - optionnel
            $table->string('prodcom_code', 50)->nullable();

            // Organisme déclarant - obligatoire
            $table->string('declaring_organization', 255);

            // Pays organisme (liste pays) - obligatoire
            $table->foreignId('organization_country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            // Adresse organisme - optionnel
            $table->text('organization_address')->nullable();

            // Code postal (numérique, entier) - optionnel
            $table->unsignedInteger('postal_code')->nullable();

            // Description de l'article (texte long, 3000 car max) - obligatoire
            // -> on stocke en TEXT et on applique la limite 3000 côté validation API
            $table->text('item_description');

            // Statut volet 1 (gris/orange/vert)
            $table->enum('volet_1_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_1_completed')->default(false);

            // =========================
            // VOLET 2 / VOLET 3
            // =========================
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->nullOnDelete();
            $table->enum('volet_2_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_2_completed')->default(false);

            $table->enum('volet_3_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_3_completed')->default(false);

            // Metadata
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'volet_1_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};