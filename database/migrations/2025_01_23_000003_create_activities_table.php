<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title_nl');
            $table->string('title_fr');
            $table->text('content_nl');
            $table->text('content_fr');
            $table->string('activity_type')->default('kidicalmass');
            $table->dateTime('begin_date');
            $table->dateTime('end_date');
            $table->string('location');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('activity_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_group');
        Schema::dropIfExists('activities');
    }
};
