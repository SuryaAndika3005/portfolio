<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // role/client/year/tools/gallery_images already exist via
            // 2026_07_25_000001_add_metadata_to_projects_table — only the
            // fields below are new here.
            $table->boolean('is_highlighted')->default(false)->after('category_id');
            $table->unsignedInteger('featured_order')->nullable()->after('is_highlighted');

            // Optional case-study fields. Nullable — the detail page falls
            // back to the plain `description` when these are empty.
            $table->text('problem')->nullable()->after('description');
            $table->text('process')->nullable()->after('problem');
            $table->text('result')->nullable()->after('process');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'is_highlighted', 'featured_order', 'problem', 'process', 'result',
            ]);
        });
    }
};
