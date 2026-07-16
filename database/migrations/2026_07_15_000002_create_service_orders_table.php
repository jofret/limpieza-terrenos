<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->date('work_date');
            $table->string('status')->default('pending');
            $table->decimal('price', 10, 2)->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
