<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('master_data', function (Blueprint $table) {
            // Add composite unique index
            $table->unique(['type', 'name'], 'unique_type_name');
        });
    }

    public function down(): void
    {
        Schema::table('master_data', function (Blueprint $table) {
            // Drop the unique index
            $table->dropUnique('unique_type_name');
        });
    }
};
