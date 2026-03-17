<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('zone')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('customer_type', ['casa', 'empresa', 'consorcio', 'country', 'otro'])->default('casa');
            $table->enum('status', ['activo', 'inactivo', 'potencial'])->default('potencial');
            $table->enum('preferred_contact', ['whatsapp', 'email', 'telefono'])->default('whatsapp');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};