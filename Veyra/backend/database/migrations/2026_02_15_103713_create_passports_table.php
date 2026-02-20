<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passports', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // QR Code: Oui/Non (obligatoire)
            $table->boolean('with_qr');

            // status: draft (save-progress) / final (validate-step)
            $table->enum('status', ['draft', 'final'])->default('draft');

            // ========= Environmental / Generation =========
            $table->json('environmental_summary')->nullable();
            $table->boolean('is_generated')->default(false);
            $table->timestamp('generated_at')->nullable();
            $table->json('payload_snapshot')->nullable();

            // ========= Access / Sharing =========
            // internal | partner | public
            $table->enum('access_level', ['internal', 'partner', 'public'])->default('internal');

            // partner emails (si access_level = partner)
            $table->json('partner_emails')->nullable();

            // public token (si access_level = public)
            $table->string('public_token', 64)->nullable();

            // date publication
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            // 1 passeport max par produit
            $table->unique('product_id');

            // token public unique (en MySQL, plusieurs NULL sont ok)
            $table->unique('public_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passports');
    }
};
