<?php

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

function error($message, $data, $code = null) {
    $d = [
        'code' => $code?? ResponseAlias::HTTP_BAD_REQUEST,
        'status' => 'error',
        'message' => $message,
        'data' => $data
    ];

    return response()->json($d, $d['code']);
}


function failed($message, $data, $code = null) {
    $d = [
        'code' => $code?? ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
        'status' => 'failed',
        'message' => $message,
        'data' => $data
    ];

    return response()->json($d, $d['code']);
}


function success($message, $data, $code = null) {
    $d = [
        'code' => $code?? ResponseAlias::HTTP_OK,
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ];
//    return response('Hello World', 200)
//        ->header();
    return response()->json($d, $d['code']);
}


function expired($message, $data, $code = null) {
    $d = [
        'code' => $code?? ResponseAlias::HTTP_UNAUTHORIZED,
        'status' => 'expired',
        'message' => $message,
        'data' => $data
    ];
    return response()->json($d, $d['code']);
}
