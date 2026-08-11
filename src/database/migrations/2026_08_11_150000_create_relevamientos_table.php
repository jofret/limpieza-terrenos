<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relevamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->string('category_other')->nullable();
            $table->string('property_type')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pendiente');
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time_from')->nullable();
            $table->time('scheduled_time_to')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reopen_requested_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('requires_non_compete_clause')->default(false);
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->unsignedInteger('workers_count')->nullable();
            $table->unsignedInteger('estimated_duration_days')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relevamientos');
    }
};
