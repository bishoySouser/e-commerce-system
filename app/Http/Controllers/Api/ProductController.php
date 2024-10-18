<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Filters\ProductQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = $this->generateCacheKey($request);
        
        return Cache::remember($cacheKey, 3600, function () use ($request) {
            $filter = new ProductQuery();
            $queryItems = $filter->transform($request);

            $products = count($queryItems) === 0
                ? Product::paginate()
                : Product::where($queryItems)->paginate();

            return $this->sendResponse(
                ProductResource::collection($products),
                'Products retrieved successfully.'
            );
        });

    }

}
