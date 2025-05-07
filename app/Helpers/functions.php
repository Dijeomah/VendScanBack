<?php

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

    function authUser(): User|JsonResponse|Authenticatable|null
    {
        try {
            $user = auth()->user();
            // $user = Auth::class;
        }
        catch(Exception $e) {
            return error($e->getMessage(), null, ResponseAlias::HTTP_UNAUTHORIZED);
        }
        return $user;
    }
