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
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'customer_order_id')) {
                $table->foreignId('customer_order_id')
                    ->nullable()
                    ->after('customer_id')
                    ->constrained('customer_orders')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('sales', 'cancelled_at')) {
                $table->timestamp('cancelled_at')
                    ->nullable()
                    ->after('status');
            }

            if (! Schema::hasColumn('sales', 'cancelled_by')) {
                $table->foreignId('cancelled_by')
                    ->nullable()
                    ->after('cancelled_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('sales', 'cancel_note')) {
                $table->text('cancel_note')
                    ->nullable()
                    ->after('cancelled_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'customer_order_id')) {
                $table->dropForeign(['customer_order_id']);
            }

            if (Schema::hasColumn('sales', 'cancelled_by')) {
                $table->dropForeign(['cancelled_by']);
            }

            $columns = [];

            foreach ([
                'customer_order_id',
                'cancelled_at',
                'cancelled_by',
                'cancel_note',
            ] as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $columns[] = $column;
                }
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
