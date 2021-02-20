<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ExampleController extends Controller
{
    public function getWelcome() {
        return app()->version();
    }

    public function getTest() {
        $product = Product::create(['external_id' => 123, 'name' => 'cuequinha do batima']);

        dd($product);
    }

}
