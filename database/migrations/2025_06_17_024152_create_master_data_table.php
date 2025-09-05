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
        Schema::create('master_data', function (Blueprint $table) {
            $table->id();
            $table->string('type');         // e.g. 'company','blood_group', 'grade', 'bank'
            $table->string('name');         // e.g. 'A+', 'Grade 1', 'Bank Asia'
            $table->string('code')->nullable(); // Optional short code
            $table->unsignedBigInteger('parent_id')->nullable(); // For hierarchy (e.g., branch under company)
            $table->integer('order')->nullable(); // Optional sort order
            $table->string('description')->nullable(); // Optional sort order
            $table->boolean('status')->default(true); // Active/inactive
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            //Indexes for performance
            $table->index(['type', 'name']);
            $table->index('code');
            $table->index('parent_id');
            $table->index('status');
            
            $table->foreign('parent_id')->references('id')->on('master_data')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
