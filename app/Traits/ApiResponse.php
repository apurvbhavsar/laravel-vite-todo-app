<?php
namespace App\Traits;

trait ApiResponse{

    /**
     * Json Error Response
     *
     * @param string $message
     * @param integer $code
     * @return void
     */
    public function error($message, $code = 500)
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $code);
    }

    /**
     * Return Server Validation Error Response
     *
     * @param array $errors
     * @param string $message
     * @return void
     */
    public function validationError($errors, $message)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], 401);
    }

    /**
     * Return success response with data
     *
     * @param array $data
     * @param string $message
     * @param integer $code
     * @return void
     */
    public function success($data, $message = "Operation success", $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
