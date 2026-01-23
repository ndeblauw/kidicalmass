<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->string('title_nl');
            $table->string('title_fr');
            $table->text('content_nl');
            $table->text('content_fr');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('article_group', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
        });
    }
};
