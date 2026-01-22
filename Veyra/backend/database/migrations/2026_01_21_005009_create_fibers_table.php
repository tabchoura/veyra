<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fibers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            // "Fibre" = matériau tiré de la table materials (feuille Excel "Materials")
            $table->foreignId('fiber_id')
                ->constrained('materials')
                ->onDelete('restrict');

            // Pourcentage 0-100 (contrôle somme à 100% via controller)
            $table->decimal('percentage', 5, 2)->unsigned();

            // Origine = pays
            $table->foreignId('origin_country_id')
                ->constrained('countries')
                ->onDelete('restrict');

            // Optionnels
            $table->date('transaction_date')->nullable();

            // Certification (Oui/Non) -> si oui: num + date validité obligatoires
            $table->boolean('has_certification')->default(false);
            $table->string('certificate_number', 255)->nullable();
            $table->date('validity_date')->nullable();
            $table->string('transaction_reference', 255)->nullable(); // non obligatoire même si certif = oui

            // Audit client (Oui/Non) -> si oui: commentaire obligatoire
            $table->boolean('has_client_audit')->default(false);
            $table->text('audit_comments')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fibers');
    }
};