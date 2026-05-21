<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Unit;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Air Mineral', 'description' => 'Produk air mineral kemasan.'],
            ['name' => 'Teh Kemasan', 'description' => 'Produk minuman teh dalam kemasan.'],
            ['name' => 'Minuman Bersoda', 'description' => 'Produk minuman ringan bersoda.'],
            ['name' => 'Minuman Isotonik', 'description' => 'Produk minuman isotonik dan energi.'],
            ['name' => 'Kopi Kemasan', 'description' => 'Produk kopi siap minum dalam kemasan.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }

        $units = [
            ['name' => 'Botol', 'abbreviation' => 'btl'],
            ['name' => 'Dus', 'abbreviation' => 'dus'],
            ['name' => 'Karton', 'abbreviation' => 'ktn'],
            ['name' => 'Krat', 'abbreviation' => 'krat'],
            ['name' => 'Pcs', 'abbreviation' => 'pcs'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['abbreviation' => $unit['abbreviation']],
                ['name' => $unit['name']]
            );
        }

        $botol = Unit::query()->where('abbreviation', '=', 'btl')->first();
        $dus = Unit::query()->where('abbreviation', '=', 'dus')->first();

        $airMineral = Category::query()->where('slug', '=', 'air-mineral')->first();
        $tehKemasan = Category::query()->where('slug', '=', 'teh-kemasan')->first();
        $soda = Category::query()->where('slug', '=', 'minuman-bersoda')->first();
        $isotonik = Category::query()->where('slug', '=', 'minuman-isotonik')->first();
        $kopi = Category::query()->where('slug', '=', 'kopi-kemasan')->first();

        $products = [
            [
                'category_id' => $airMineral->id,
                'unit_id' => $dus->id,
                'name' => 'Aqua 600ml Dus',
                'sku' => 'AG-AQM-600-DUS',
                'barcode' => '8991002100011',
                'purchase_price' => 36000,
                'selling_price' => 42000,
                'average_cost' => 36000,
                'stock' => 50,
                'minimum_stock' => 10,
                'bulk_prices' => [
                    ['min_qty' => 1, 'max_qty' => 4, 'price' => 42000],
                    ['min_qty' => 5, 'max_qty' => 9, 'price' => 40500],
                    ['min_qty' => 10, 'max_qty' => null, 'price' => 39000],
                ],
            ],
            [
                'category_id' => $tehKemasan->id,
                'unit_id' => $dus->id,
                'name' => 'Teh Pucuk Harum 350ml Dus',
                'sku' => 'AG-TPH-350-DUS',
                'barcode' => '8996001600012',
                'purchase_price' => 52000,
                'selling_price' => 60000,
                'average_cost' => 52000,
                'stock' => 35,
                'minimum_stock' => 8,
                'bulk_prices' => [
                    ['min_qty' => 1, 'max_qty' => 3, 'price' => 60000],
                    ['min_qty' => 4, 'max_qty' => 9, 'price' => 58000],
                    ['min_qty' => 10, 'max_qty' => null, 'price' => 56000],
                ],
            ],
            [
                'category_id' => $soda->id,
                'unit_id' => $dus->id,
                'name' => 'Coca Cola 390ml Dus',
                'sku' => 'AG-CC-390-DUS',
                'barcode' => '8992761120013',
                'purchase_price' => 68000,
                'selling_price' => 76000,
                'average_cost' => 68000,
                'stock' => 24,
                'minimum_stock' => 6,
                'bulk_prices' => [
                    ['min_qty' => 1, 'max_qty' => 2, 'price' => 76000],
                    ['min_qty' => 3, 'max_qty' => 7, 'price' => 74000],
                    ['min_qty' => 8, 'max_qty' => null, 'price' => 72000],
                ],
            ],
            [
                'category_id' => $isotonik->id,
                'unit_id' => $dus->id,
                'name' => 'Pocari Sweat 500ml Dus',
                'sku' => 'AG-PS-500-DUS',
                'barcode' => '8997035600014',
                'purchase_price' => 82000,
                'selling_price' => 92000,
                'average_cost' => 82000,
                'stock' => 18,
                'minimum_stock' => 5,
                'bulk_prices' => [
                    ['min_qty' => 1, 'max_qty' => 2, 'price' => 92000],
                    ['min_qty' => 3, 'max_qty' => 5, 'price' => 89500],
                    ['min_qty' => 6, 'max_qty' => null, 'price' => 87000],
                ],
            ],
            [
                'category_id' => $kopi->id,
                'unit_id' => $dus->id,
                'name' => 'Kopi Kapal Api Mantap Dus',
                'sku' => 'AG-KKA-MTP-DUS',
                'barcode' => '8998899000015',
                'purchase_price' => 46000,
                'selling_price' => 53000,
                'average_cost' => 46000,
                'stock' => 40,
                'minimum_stock' => 10,
                'bulk_prices' => [
                    ['min_qty' => 1, 'max_qty' => 4, 'price' => 53000],
                    ['min_qty' => 5, 'max_qty' => 11, 'price' => 51000],
                    ['min_qty' => 12, 'max_qty' => null, 'price' => 49000],
                ],
            ],
        ];

        foreach ($products as $item) {
            $bulkPrices = $item['bulk_prices'];
            unset($item['bulk_prices']);

            $product = Product::updateOrCreate(
                ['sku' => $item['sku']],
                array_merge($item, [
                    'slug' => Str::slug($item['name']),
                    'description' => 'Produk contoh untuk kebutuhan prototype sistem POS CV Ari Gita Grosir.',
                    'is_active' => true,
                ])
            );

            ProductPrice::query()
            ->where('product_id', '=', $product->id)
            ->delete();

            foreach ($bulkPrices as $price) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'min_qty' => $price['min_qty'],
                    'max_qty' => $price['max_qty'],
                    'price' => $price['price'],
                ]);
            }
        }
    }
}
