<?php

namespace Devsbuddy\AdminrCore\Traits;

trait HasResponse
{

    /**
     * @param $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data, $statusCode = 200)
    {
        if (is_array($data)) {
            return response()->json($data, $statusCode);
        }
        return response()->json(['message' => "success", 'data' => $data], $statusCode);
    }

    /**
     * @param $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function successMessage($message, $statusCode = 200)
    {
        return response()->json(['message' => $message], $statusCode);
    }


    /**
     * @param $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message, $statusCode = 500)
    {
        return response()->json(['message' => $message], $statusCode);
    }
}
