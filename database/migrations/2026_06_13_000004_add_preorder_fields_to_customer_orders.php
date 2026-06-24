<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE customer_orders MODIFY status ENUM('pending', 'preorder', 'converted', 'rejected', 'cancelled') DEFAULT 'pending'");
        }

        Schema::table('customer_order_items', function (Blueprint $table) {
            $table->integer('available_quantity')->default(0)->after('quantity');
            $table->integer('preorder_quantity')->default(0)->after('available_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('customer_order_items', function (Blueprint $table) {
            $table->dropColumn(['available_quantity', 'preorder_quantity']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE customer_orders MODIFY status ENUM('pending', 'converted', 'rejected', 'cancelled') DEFAULT 'pending'");
        }
    }
};
