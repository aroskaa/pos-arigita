<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'selling_price')) {
            // Migrate existing selling_price to product_prices table for min_qty = 1 if not exists
            $products = DB::table('products')->get();

            foreach ($products as $product) {
                $hasTierOne = DB::table('product_prices')
                    ->where('product_id', '=', $product->id)
                    ->where('min_qty', '=', 1)
                    ->exists();

                if (! $hasTierOne && isset($product->selling_price)) {
                    // Check if there are other tiers to determine max_qty for tier 1
                    $nextTier = DB::table('product_prices')
                        ->where('product_id', '=', $product->id)
                        ->where('min_qty', '>', 1)
                        ->orderBy('min_qty', 'asc')
                        ->first();

                    $maxQty = $nextTier ? ($nextTier->min_qty - 1) : null;

                    DB::table('product_prices')->insert([
                        'product_id' => $product->id,
                        'min_qty' => 1,
                        'max_qty' => $maxQty,
                        'price' => $product->selling_price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Drop selling_price column from products
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('selling_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('products', 'selling_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('selling_price', 15, 2)->default(0)->after('purchase_price');
            });

            // Restore selling_price from product_prices min_qty = 1
            $products = DB::table('products')->get();
            foreach ($products as $product) {
                $tierOne = DB::table('product_prices')
                    ->where('product_id', '=', $product->id)
                    ->where('min_qty', '=', 1)
                    ->first();

                if ($tierOne) {
                    DB::table('products')
                        ->where('id', '=', $product->id)
                        ->update(['selling_price' => $tierOne->price]);
                }
            }
        }
    }
};
