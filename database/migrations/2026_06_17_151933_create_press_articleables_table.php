<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('press_articleables', function (Blueprint $table) {
            $table->foreignId('press_article_id')->constrained()->cascadeOnDelete();
            $table->morphs('press_articleable', 'press_articleable_morph_index');
            $table->unique(['press_article_id', 'press_articleable_id', 'press_articleable_type'], 'press_articleable_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('press_articleables');
    }
};
