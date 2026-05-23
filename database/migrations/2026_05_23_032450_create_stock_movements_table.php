<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();

            $table->enum('type', [
                'purchase',
                'sale',
                'adjustment',
                'return',
                'order_cancel',
            ]);

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->integer('quantity_in')->default(0);
            $table->integer('quantity_out')->default(0);

            $table->integer('stock_before');
            $table->integer('stock_after');

            $table->decimal('average_cost_before', 15, 2)->default(0);
            $table->decimal('average_cost_after', 15, 2)->default(0);

            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['product_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
