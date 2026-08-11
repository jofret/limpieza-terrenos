<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->foreignId('property_id')->nullable()->change();
            $table->foreign('property_id')->references('id')->on('properties')->nullOnDelete();

            $table->foreignId('category_id')->nullable()->change();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();

            $table->foreignId('customer_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('relevamiento_id')->nullable()->after('property_id')->constrained('relevamientos')->nullOnDelete();
            $table->string('flow_type')->default('con_relevamiento')->after('relevamiento_id');
            $table->string('category_other')->nullable()->after('category_id');
            $table->string('time_slot')->nullable()->after('work_date');
            $table->decimal('final_price', 12, 2)->nullable()->after('price');
            $table->text('final_price_notes')->nullable()->after('final_price');
            $table->text('budget_comment')->nullable()->after('final_price_notes');
            $table->string('budget_token')->nullable()->unique()->after('budget_comment');
            $table->timestamp('budget_sent_at')->nullable()->after('budget_token');
            $table->timestamp('budget_accepted_at')->nullable()->after('budget_sent_at');
            $table->json('payment_method_preference')->nullable()->after('budget_accepted_at');
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->string('status')->default('visita_programada')->change();
            $table->date('work_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['relevamiento_id']);
            $table->dropColumn([
                'customer_id', 'relevamiento_id', 'flow_type', 'category_other', 'time_slot',
                'final_price', 'final_price_notes', 'budget_comment', 'budget_token',
                'budget_sent_at', 'budget_accepted_at', 'payment_method_preference',
            ]);
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->foreignId('property_id')->nullable(false)->change();
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable(false)->change();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->string('status')->default('pending')->change();
            $table->date('work_date')->nullable(false)->change();
        });
    }
};
