<?php

namespace App\Classes;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
{
    public static function throw($e, $message ="Something went wrong! Process not completed"){
        Log::info($e);
        throw new HttpResponseException(response()->json(["message"=> $message], 500));
    }

    public static function validateErrors($message , $code=422): \Illuminate\Http\JsonResponse
    {
        $response['errors'] = $message;
        return response()->json($response, $code);
    }

    public static function sendResponse($result , $message ,$code=200): \Illuminate\Http\JsonResponse
    {
        $response=['data' => $result];
        if(!empty($message)){
            $response['message'] =$message;
        }
        return response()->json($response, $code);
    }
}
