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
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_record_id')->constrained()->onDelete('cascade');
            $table->string('employee_name');
            $table->string('position')->nullable();
            $table->string('whatsapp')->nullable();
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->decimal('base_salary', 12, 2);
            $table->decimal('total_sessions', 12, 2)->default(0);
            $table->decimal('session_bonus', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('total_compensation', 12, 2);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2);
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
