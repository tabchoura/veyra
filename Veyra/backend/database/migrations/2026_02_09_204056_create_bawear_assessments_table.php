<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('bawear_assessments', function (Blueprint $table) {
      $table->id();

      $table->foreignId('product_id')->constrained()->cascadeOnDelete();

      // pdf | api
      $table->string('source_type', 10)->index();

      $table->string('pdf_path')->nullable();
      $table->json('extracted_payload')->nullable();
      $table->json('normalized_payload')->nullable();

      $table->decimal('score_value', 10, 4)->nullable();
      $table->string('score_unit', 20)->nullable();

      // draft | processed | error
      $table->string('status', 20)->default('draft')->index();
      $table->json('errors')->nullable();

      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('bawear_assessments');
  }
};
