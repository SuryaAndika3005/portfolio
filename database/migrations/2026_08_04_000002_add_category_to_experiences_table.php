<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            // Groups the "Journey" timeline on the homepage into columns:
            // Professional Work / Organization / Events (or any label you use).
            $table->string('category')->default('Professional Work')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
