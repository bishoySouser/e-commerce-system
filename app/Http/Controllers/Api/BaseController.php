<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($result = "", $message) {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404) {
        $response = [
            'success' => false,
            'message' => $error
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Generate a unique cache key based on the request parameters
     */
    protected function generateCacheKey(Request $request): string
    {
        $queryParams = $request->query();
        ksort($queryParams); // Sort to ensure consistent cache keys
        
        return 'products:' . md5(serialize($queryParams) . $request->query('page', 1));
    }
}
