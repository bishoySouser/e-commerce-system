<?php

namespace Tests\Unit;

use App\Filters\ProductQuery;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ProductQueryTest extends TestCase
{
    private ProductQuery $productQuery;

    protected function setUp(): void {
        parent::setUp();
        $this->productQuery = new ProductQuery();
    }

    public function test_can_transform_range_price_filters() {
        $request = new Request([
            'price' => [
                'lte' => '300',
                'gte' => '20'
            ]
        ]);

        $result = $this->productQuery->transform($request);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertContains(['price', '<=', '300'], $result);
        $this->assertContains(['price', '>=', '20'], $result);
    }
}
