<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($result = "", $message, $code = 200) {
        $response = [
            'success' => true,
            'status_code' => $code,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404) {
        $response = [
            'success' => false,
            'status_code' => $code,
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
