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
            $table->string('item_code', 50)->unique();
            $table->timestamp('creation_datetime')->useCurrent();

            $table->string('product_image', 255);
            $table->string('product_name', 255);
            $table->decimal('weight', 10, 3)->unsigned();


            $table->string('batch_serial', 100)->nullable();
            $table->string('prodcom_code', 50)->nullable();

            $table->string('declaring_organization', 255);

            $table->foreignId('organization_country_id')
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->text('organization_address')->nullable();
            $table->unsignedInteger('postal_code')->nullable();

            $table->text('item_description');

            $table->enum('volet_1_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_1_completed')->default(false);

            // =========================
            // VOLET 2 — TYPE DE PRODUIT
            // =========================
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->nullOnDelete();
            $table->enum('volet_2_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_2_completed')->default(false);

            // =========================
            // VOLET 3 — FIBRES
            // =========================
            $table->enum('volet_3_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_3_completed')->default(false);

            // =========================
            // VOLET 4 — YARNS
            // =========================
            $table->enum('volet_4_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_4_completed')->default(false);

            // =========================
            // VOLET 5 — FABRICS
            // =========================
            $table->enum('volet_5_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_5_completed')->default(false);

            // =========================
            // CERTIFICATION (si tu la gardes dans products)
            // =========================
            $table->boolean('has_certification')->default(false);
            $table->string('certification_name')->nullable();
            $table->string('certificate_number')->nullable();
            $table->date('certificate_valid_until')->nullable();

            // =========================
            // STEP 6 — MANUFACTURING
            // =========================
            $table->enum('step_6_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('step_6_completed')->default(false);

            // =========================
            // VOLET 7 — ACCESSORIES
            // =========================
            $table->enum('volet_7_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_7_completed')->default(false);

            // =========================
            // VOLET 8
            // =========================
            $table->enum('volet_8_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_8_completed')->default(false);

            // =========================
            // VOLET 9 (statut seulement, les données sont dans end_of_lives)
            // =========================
            $table->enum('volet_9_status', ['grey', 'orange', 'green'])->default('grey');
            $table->boolean('volet_9_completed')->default(false);

            // =========================
            // METADATA
            // =========================
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
