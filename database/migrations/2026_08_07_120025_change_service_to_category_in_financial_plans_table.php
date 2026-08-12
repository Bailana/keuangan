<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_plans', function (Blueprint $table) {
            $table->dropColumn('service');
            $table->string('category')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('financial_plans', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->enum('service', ['terapi', 'sekolah', 'vokasi', 'semua'])->after('type');
        });
    }
};
