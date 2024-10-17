<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Filters\ProductQuery;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new ProductQuery();
        $queryItems = $filter->transform($request);

        if (count($queryItems) == 0) {
            $products = Product::paginate();
        }else {
            $products = Product::where($queryItems)->paginate();
        }

        return $this->sendResponse(
            ProductResource::collection($products),
            'Products retrieved successfully.'
        );

    }

}
