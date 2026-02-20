<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_repairs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_usage_id')->constrained('product_usages')->cascadeOnDelete();

            // Required (CDC)
            $table->date('repair_date');
            $table->foreignId('repair_action_id')->constrained('repair_actions');
            $table->foreignId('country_id')->constrained('countries');

            // Optional (CDC)
            $table->string('organization')->nullable();
            $table->string('other_text')->nullable(); // if action is "Other" (optional)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_repairs');
    }
};
