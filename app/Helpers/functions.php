<?php

    use Illuminate\Http\Response;
    use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

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
