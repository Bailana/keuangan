<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->date('salary_date');
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('salary_extra', 12, 2)->default(0);
            $table->decimal('total_sessions', 12, 2)->default(0);
            $table->decimal('session_bonus', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('total_compensation', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};
