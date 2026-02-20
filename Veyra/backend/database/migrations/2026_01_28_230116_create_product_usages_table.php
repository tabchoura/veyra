<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unique('product_id');

            // General
            $table->string('brand')->nullable();

            // Required (CDC)
            $table->foreignId('delivery_country_id')->constrained('countries');
            $table->date('delivery_date');

            // Care / Maintenance (CDC)
            $table->unsignedSmallInteger('washing_temperature')->nullable();
            $table->boolean('hand_wash')->default(false);
            $table->boolean('machine_wash')->default(false);
            $table->boolean('dry_clean')->default(false);
            $table->boolean('bleach')->default(false);
            $table->boolean('dry_shade')->default(false);
            $table->boolean('tumble_dry')->default(false);
            $table->boolean('ironing')->default(false);

            // Repairability (CDC)
            $table->boolean('is_repairable')->default(false);
            $table->text('repair_comment')->nullable(); // required if is_repairable=true (validation in controller)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_usages');
    }
};
