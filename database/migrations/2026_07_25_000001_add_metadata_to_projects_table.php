<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('role')->nullable()->after('title');
            $table->string('client')->nullable()->after('role');
            $table->string('year')->nullable()->after('client');
            $table->string('tools')->nullable()->after('year');
            // Resolved gallery image paths, stored once at seed/upload time
            // instead of being probed with file_exists() on every page render.
            $table->json('gallery_images')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['role', 'client', 'year', 'tools', 'gallery_images']);
        });
    }
};
