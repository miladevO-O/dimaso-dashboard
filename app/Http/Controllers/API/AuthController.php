<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Support\Exceptions\OAuthException;
use App\Support\Traits\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use Authenticatable;

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->credentials();

        $user = User::where('user_email', $credentials['user_email'])->first();

        if (!$user) {
            throw new OAuthException(code: 'invalid_credentials_provided');
        }

        $stored_hash = $user->user_pass;
        $password = $credentials['password'];
        $prefix = '$wp';

        // Check if the stored hash has the custom '$wp' prefix
        if (strpos($stored_hash, $prefix) === 0) {
            $bcrypt_hash = substr($stored_hash, strlen($prefix));
        } else {
            $bcrypt_hash = $stored_hash; 
        }

        // Use Laravel's built-in Hash checker, which is the correct way for bcrypt hashes
        if (!Hash::check($password, $bcrypt_hash)) {
            throw new OAuthException(code: 'invalid_credentials_provided');
        }

        // Password is correct, log the user in
        if (!$token = auth()->login($user)) {
            throw new OAuthException(code: 'invalid_credentials_provided');
        }

        return $this->responseWithToken(access_token: $token);
    }

    /**
     * Refresh a token.
     *
     * @return \App\Modules\Auth\Collections\TokenResource
     */
    public function refresh(): JsonResponse
    {
        return $this->responseWithToken(access_token: auth()->refresh());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return new JsonResponse(['sucess' => true]);
    }
}
