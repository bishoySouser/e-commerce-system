<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ProductQuery {
    protected $safeParms = [
        "name" => ['eq'],
        "price" => ['gte', 'lte'],
        "stockQuantity" => ['gte', 'lte'],
        "category" => ['eq']
    ];

    protected $columnMap = [
        'stockQuantity' => 'stock_quantity'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lte' => '<=',
        'gte' => '>='
    ];

    public function transform(Request $request) {
        $eloQuery = [];

        foreach ($this->safeParms as $parm => $operators) {
            $query = $request->query($parm);

            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$parm] ?? $parm;

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column , $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        return $eloQuery;
    }
} 