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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('hide_spoilers_default')->default(false)->after('bio');
            $table->boolean('show_views_count')->default(true)->after('hide_spoilers_default');
            $table->boolean('private_profile')->default(false)->after('show_views_count');
            $table->boolean('hide_email')->default(false)->after('private_profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['hide_spoilers_default', 'show_views_count', 'private_profile', 'hide_email']);
        });
    }
};
