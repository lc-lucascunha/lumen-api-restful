<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        factory(Product::class, 10)->create();
    }
}
