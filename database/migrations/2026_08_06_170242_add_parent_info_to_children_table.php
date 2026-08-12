<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->string('parent_name')->nullable()->after('name');
            $table->string('parent_whatsapp')->nullable()->after('parent_name');
            $table->string('class_name')->nullable()->after('parent_whatsapp');
            $table->decimal('monthly_fee', 12, 2)->nullable()->default(null)->after('service');
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['parent_name', 'parent_whatsapp', 'class_name', 'monthly_fee']);
        });
    }
};
