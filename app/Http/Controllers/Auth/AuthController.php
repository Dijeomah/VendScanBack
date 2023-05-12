<?php

    namespace App\Http\Controllers\Auth;

    use App\Models\City;
    use App\Models\Country;
    use App\Models\State;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\Auth;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Str;

    class AuthController extends Controller
    {
        /**
         * Create a new AuthController instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->middleware('auth:api', ['except' => ['login', 'register']]);
        }

        public function register(Request $request)
        {
            $this->validate($request, config('validation.registration'));

            $userid = $this->generateUserID();

            $userData = new User();
            $userData->userid = $userid;
            $userData->first_name = $request->first_name;
            $userData->last_name = $request->last_name;
            $userData->role = $request->role;
            $userData->phone_number = $request->phone_number;
            $userData->email = $request->email;
            $userData->password = Hash::make($request->password);
            $userData->save();
            return success('Registration Successful. ', $userData, Response::HTTP_CREATED);
        }

        public function me(): \Illuminate\Contracts\Auth\Authenticatable
        {
            $user = auth()->user();
            return $user;
        }


        /**
         * Get a JWT via given credentials.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function login()
        {
            $credentials = request(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Username/ Password incorrect'], 401);
            }
//         array_merge(['user' => $this->me()], $this->formatToken($token));
//        return  array_merge(['user' => $this->me(), $this->respondWithToken($token)]);
            return array_merge(['result' => $this->formatToken($token), 'user' => $this->me()]);
        }


        /**
         * Log the user out (Invalidate the token).
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function logout()
        {
            auth()->logout();
            return response()->json(['message' => 'Successfully logged out']);
        }

        /**
         * Refresh a token.
         *
         * @return array|User[]
         */
        public function refresh()
        {
//        return $this->respondWithToken(auth()->refresh());
            $token = auth()->refresh();
            return array_merge(['user' => $this->me()], $this->formatToken($token));

        }

        /**
         * Get the token array structure.
         *
         * @param string $token
         *
         * @return \Illuminate\Http\JsonResponse
         */
        protected function respondWithToken($token)
        {
            return response()->json([
                'message' => 'Logged in successfully',
//            'data'=>$data,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]);
        }

        private function formatToken($token)
        {
            return [
                'status' => 'success',
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ];
        }


        public function country()
        {
            $country = Country::all();

            return success("Request for Country list successful", $country);
        }

        public function state($country_id)
        {
            $state = State::where('country_id', $country_id)->get();

            return success("Request for State list successful", $state);
        }

        public function city($state_id)
        {
            $city = City::where('state_id', $state_id)->get();
            return success("Request for City list successful", $city);
        }
    }
