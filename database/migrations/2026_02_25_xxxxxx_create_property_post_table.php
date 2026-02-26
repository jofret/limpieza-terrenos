<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->enum('relation_type', ['trabajo_realizado', 'comentario', 'testimonio'])->default('trabajo_realizado');
            $table->text('comment')->nullable();
            $table->integer('rating')->nullable();
            $table->date('service_date')->nullable();
            $table->timestamps();
            
            $table->unique(['property_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_post');
    }
};