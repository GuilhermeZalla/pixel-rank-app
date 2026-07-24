<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('platforms');
        Schema::dropIfExists('genres');
    }

    public function down(): void
    {
        Schema::create('platforms', function ($table) {
        });

        Schema::create('genres', function ($table) {
        });
    }
};