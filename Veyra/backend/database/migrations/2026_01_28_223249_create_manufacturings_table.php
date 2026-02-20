<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufacturings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->string('producing_organization')->nullable();
            $table->foreignId('producing_country_id')->constrained('countries');
            $table->text('address')->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->date('production_date')->nullable();

            $table->boolean('has_certification')->default(false);
            $table->string('certificate_number')->nullable();
            $table->date('validity_date')->nullable();
            $table->string('transaction_reference')->nullable();

            $table->boolean('has_client_audit')->default(false);
            $table->text('audit_comments')->nullable();

            $table->boolean('special_effects')->default(false);

            $table->string('finishing_method')->nullable();
            $table->string('dyeing_method')->nullable();
            $table->string('finishing_treatment')->nullable();

            $table->text('comments')->nullable();

            $table->decimal('renewable_energy_percentage', 5, 2)->unsigned()->default(0);
            $table->decimal('recycled_water_percentage', 5, 2)->unsigned()->default(0);

            $table->boolean('zdhc_supply_to_zero')->default(false);
            $table->boolean('zdhc_get_zd')->default(false);

            // ✅ pas de after() dans Schema::create()
            $table->foreignId('finishing_method_id')
                ->nullable()
                ->constrained('finishing_methods')
                ->nullOnDelete();

            $table->foreignId('colouring_method_id')
                ->nullable()
                ->constrained('colouring_methods')
                ->nullOnDelete();

            $table->foreignId('finish_treatment_id')
                ->nullable()
                ->constrained('finish_treatments')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacturings');
    }
};
