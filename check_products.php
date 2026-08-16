<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = App\Models\Product::all();

echo "Total products: " . $products->count() . "\n\n";

foreach ($products as $product) {
    echo "ID: {$product->id}, Name: {$product->name}, Category ID: {$product->category_id}, Seasonal: " . ($product->is_seasonal ? 'Yes' : 'No') . ", Exotic: " . ($product->is_exotic ? 'Yes' : 'No') . "\n";
}
