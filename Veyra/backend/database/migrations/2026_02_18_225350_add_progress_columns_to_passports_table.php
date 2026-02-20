<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('passports', function (Blueprint $table) {
            $table->unsignedTinyInteger('completed_steps')->default(0)->after('status');
            $table->unsignedTinyInteger('total_steps')->default(13)->after('completed_steps');
        });
    }

    public function down(): void
    {
        Schema::table('passports', function (Blueprint $table) {
            $table->dropColumn(['completed_steps','total_steps']);
        });
    }
};
