<?php

namespace App\Http\Controllers\Auth;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Mail\WelcomeMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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

        // Get free plan
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        $userData = new User();
        $userData->userid = $userid;
        $userData->first_name = $request->first_name;
        $userData->last_name = $request->last_name;
        $userData->role = $request->role ?? 'vendor';
        $userData->phone_number = $request->phone_number;
        $userData->email = $request->email;
        $userData->password = Hash::make($request->password);
        $userData->subscription_plan_id = $freePlan ? $freePlan->id : null;
        $userData->save();

        // Create active subscription for vendor
        if ($userData->role === 'vendor' && $freePlan) {
            Subscription::create([
                'user_id' => $userData->id,
                'subscription_plan_id' => $freePlan->id,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => null, // Free plan never expires
            ]);
        }

        // Send welcome email
        try {
            Mail::to($userData->email)->send(new WelcomeMail($userData));
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email: ' . $e->getMessage());
            // Don't fail registration if email fails
        }

        Log::info('New user registered', [
            'user_id' => $userData->id,
            'email' => $userData->email,
            'role' => $userData->role,
        ]);

        return success('Registration Successful. ', $userData, Response::HTTP_CREATED);
    }

    public function me(): \Illuminate\Contracts\Auth\Authenticatable
    {
        return auth()->user();
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return error('Unauthorized', null, Response::HTTP_UNAUTHORIZED);
        }

        return success('Login successful', [
            'user' => $this->me(),
            'token' => $this->formatToken($token)
        ], Response::HTTP_OK);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
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
     * @return JsonResponse
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
