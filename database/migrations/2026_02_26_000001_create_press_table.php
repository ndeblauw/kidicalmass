<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('press', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('publication_date');
            $table->string('outlet');
            $table->string('media_type');
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('visible')->default(true);
            $table->boolean('highlighted')->default(false);
            $table->timestamps();
        });

        Schema::create('group_press', function (Blueprint $table) {
            $table->foreignId('press_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_press');
        Schema::dropIfExists('press');
    }
};
