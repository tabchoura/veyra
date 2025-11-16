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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // === AUTHENTIFICATION ===
            $table->string('email')->unique();
            $table->string('password');
            // ⚠️ On ne stocke pas la confirmation du mot de passe en BDD

            $table->boolean('email_verified')->default(false);
            $table->string('email_verification_token')->nullable();

            // === INFORMATIONS ENTREPRISE ===
            $table->string('company_name');         // Nom de la société
            $table->string('logo_url')->nullable(); // Chemin du logo
            $table->string('tva_number');           // Code TVA (tu peux ajouter ->unique() si besoin)

            // === CONTACT PERSON ===
            $table->string('first_name');
            $table->string('last_name');
            $table->string('function')->nullable(); // Fonction

            // === ADRESSE ===
            $table->string('address1');      // Adresse ligne 1
            $table->string('address2')->nullable(); // Adresse ligne 2
            $table->string('postal_code');   // Code postal
            // Plus simple : on stocke juste le nom du pays
            $table->string('country');       // Pays (valeur issue de la liste côté front)

            // === SECTEUR D'ACTIVITÉ ===
            $table->enum('sector', [
                'Textile & mode',
                'Agroalimentaire',
                'Électronique & technologies',
                'Construction & BTP',
                'Industrie manufacturière',
                'Énergie (production, distribution, stockage)',
                'Immobilier & gestion d’actifs',
                'Chimie, plasturgie & matériaux',
                'Emballage & logistique',
                'Automobile & mobilité',
                'Autres',
            ]);
            $table->string('sector_other')->nullable(); // Autre secteur (si "Autres")

            // === PARTENAIRES ===
            $table->enum('partner', ['bAwear Score', 'FTTH', 'MODINT', 'CBI', 'Autre'])->nullable();
            $table->string('partner_other')->nullable(); // Autre partenaire

            // === PRÉFÉRENCES ===
            $table->enum('language', ['fr', 'en'])->default('fr'); // Langue interface

            // === TRACKING ===
            $table->string('ip_signup')->nullable();   // IP d'inscription
            $table->timestamp('signup_at')->nullable(); // Date/heure d’inscription

            // === GESTION UTILISATEUR (ADMIN) ===
            $table->enum('user_type', ['ADMIN', 'USER', 'SUPER_USER'])->default('SUPER_USER');

               $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');
            // 0 = Pas d’accès / 1 = Accès total / 2 = Lecture seule
            $table->unsignedTinyInteger('dpp_access')->default(0);
            $table->integer('quota_dpp')->default(0); // Quota DPP

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
