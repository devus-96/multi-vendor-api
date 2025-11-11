<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreateProductController extends Controller
{
    public function __invoke (Request $request, Validator $validator) {
        $validator->make($request->all(), [
            'type'                => 'required',
            'sku'                 => ['required', 'unique:products,sku', new Slug],
        ]);
    }
}
