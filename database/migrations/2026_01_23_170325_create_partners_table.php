<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')->nullable();
            $table->string('name');
            $table->string('url')->nullable();
            $table->text('description_nl')->nullable();
            $table->text('description_fr')->nullable();
            $table->boolean('show_logo')->default(true);
            $table->boolean('visible')->default(true);

            $table->timestamps();
        });
    }
};
