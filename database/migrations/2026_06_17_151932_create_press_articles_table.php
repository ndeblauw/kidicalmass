<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('press_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title_nl');
            $table->string('title_fr');
            $table->string('outlet');
            $table->string('url', 500)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('press_articles');
    }
};
