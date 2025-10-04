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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            // frequently searched unique identifiers
            $table->string('employee_code', 50)->unique();

            $table->string('first_name', 100)->index();
            $table->string('last_name', 100)->nullable()->index();

            $table->string('email', 150)->unique();
            $table->string('phone', 20)->nullable()->index();
            $table->string('tin', 50)->nullable()->index(); // tax ID
            $table->string('national_id', 50)->nullable()->index();

            $table->date('date_of_birth')->nullable();

            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            // foreign keys
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedBigInteger('division_id')->nullable()->index();
            $table->unsignedBigInteger('department_id')->nullable()->index();
            $table->unsignedBigInteger('designation_id')->nullable()->index();

            $table->date('hire_date')->nullable()->index();
            $table->tinyInteger('status')->default(1)->index(); // active/inactive filter

            $table->text('address')->nullable();
            $table->string('emergency_contact',150)->nullable();
            $table->string('photo',255)->nullable();
       
            // Added fields
            $table->integer('sl_order')->default(0)->index(); // serial order
            $table->decimal('balance', 15, 2)->default(0); // account balance

            $table->timestamps();

            // Foreign keys (optional)
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->nullOnDelete();
            $table->foreign('designation_id')
                ->references('id')
                ->on('designations')
                ->nullOnDelete();
        });

        // Composite index example (if you often search by department & status together)
        Schema::table('employees', function (Blueprint $table) {
            $table->index(['designation_id']);
            $table->index(['department_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
