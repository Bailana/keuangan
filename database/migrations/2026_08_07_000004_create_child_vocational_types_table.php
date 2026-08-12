<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_vocational_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->foreignId('vocational_type_id')->constrained('vocational_types')->onDelete('cascade');
            $table->integer('monthly_sessions')->default(4)->comment('Jumlah sesi yang direncanakan per bulan');
            $table->timestamps();
            $table->unique(['child_id', 'vocational_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_vocational_types');
    }
};
