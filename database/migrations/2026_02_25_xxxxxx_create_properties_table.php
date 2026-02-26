<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('zone')->nullable();
            $table->integer('total_area')->nullable();
            $table->enum('property_type', ['casa', 'departamento', 'oficina', 'campo', 'quinta', 'country', 'otro'])->default('casa');
            
            // Jardines
            $table->boolean('has_garden')->default(false);
            $table->json('garden_areas')->nullable();
            
            // Piscinas
            $table->boolean('has_pool')->default(false);
            $table->json('pools')->nullable();
            
            // Árboles
            $table->boolean('has_trees')->default(false);
            $table->json('trees')->nullable();
            
            // Plantas
            $table->boolean('has_plants')->default(false);
            $table->json('plants')->nullable();
            
            // Áreas deportivas
            $table->boolean('has_sport_areas')->default(false);
            $table->json('sport_areas')->nullable();
            
            // Otros
            $table->json('other_features')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};