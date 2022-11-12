<?php

    use Illuminate\Support\Str;
    use Illuminate\Database\QueryException;
    use Illuminate\Http\Response;
    use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Support\Facades\Auth;
    use Tymon\JWTAuth\Contracts\Providers\Auth;

    function authUser() {
        try {
            $user = auth()->user();
            // $user = Auth::class;
        }
        catch(UserNotDefinedException $e) {
            return error($e->getMessage(), null, Response::HTTP_UNAUTHORIZED);
        }
        return $user;
    }
