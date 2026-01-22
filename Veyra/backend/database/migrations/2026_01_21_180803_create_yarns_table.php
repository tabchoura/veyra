<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('yarns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Info Producteur
            $table->string('producing_organization')->nullable();
            $table->foreignId('producing_country_id')->constrained('countries');
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            
            // Détails du fil (Lien vers la liste Excel)
            $table->foreignId('yarn_type_id')
                  ->nullable()
                  ->constrained('yarn_types')
                  ->onDelete('set null');

            $table->decimal('percentage', 5, 2); // 0-100
            $table->date('production_date')->nullable();
            
            // Certification
            $table->boolean('has_certification')->default(false);
            $table->string('certificate_number')->nullable();
            $table->date('validity_date')->nullable();
            $table->string('transaction_reference')->nullable();
            
            // Audit
            $table->boolean('has_client_audit')->default(false);
            $table->text('audit_comments')->nullable();
            
            // Environnement (Spécifique Volet 4)
            $table->decimal('renewable_energy_percent', 5, 2)->default(0);
            $table->decimal('recycled_water_percent', 5, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('yarns');
    }
};