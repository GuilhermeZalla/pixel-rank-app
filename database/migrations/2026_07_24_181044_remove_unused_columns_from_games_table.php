<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'developer',
                'publisher',
                'release_date',
                'cover_image',
                'trailer_url',
                'website_url',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('developer')->nullable();
            $table->string('publisher')->nullable();
            $table->date('release_date')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('website_url')->nullable();
        });
    }
};