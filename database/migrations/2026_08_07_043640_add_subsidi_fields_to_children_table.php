<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->boolean('has_subsidi')->default(false)->after('class_name');
            $table->decimal('subsidi_amount', 12, 2)->nullable()->after('has_subsidi');
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['has_subsidi', 'subsidi_amount']);
        });
    }
};
