<?php

namespace App\Http\Controllers;

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
        Log::info('Login credentials:', $credentials);

        $user = User::where('user_email', $credentials['user_email'])->first();

        if (!$user) {
            Log::info('User not found');
            throw new OAuthException(code: 'invalid_credentials_provided');
        }

        $stored_hash = $user->user_pass;
        $password = $credentials['password'];
        $prefix = '$wp';

        // Check if the stored hash has the custom '$wp' prefix
        if (strpos($stored_hash, $prefix) === 0) {
            // Strip the prefix to get the real bcrypt hash
            $bcrypt_hash = substr($stored_hash, strlen($prefix));
            Log::info('WP Prefix detected. Stripped hash:', ['hash' => $bcrypt_hash]);
        } else {
            $bcrypt_hash = $stored_hash; // Assume it's a standard hash if no prefix
        }

        // Use Laravel's built-in Hash checker, which is the correct way for bcrypt hashes
        if (!Hash::check($password, $bcrypt_hash)) {
            Log::info('Password check failed using Hash::check.', [
                'password' => $password,
                'bcrypt_hash' => $bcrypt_hash
            ]);
            throw new OAuthException(code: 'invalid_credentials_provided');
        }

        // Password is correct, log the user in
        if (!$token = auth()->login($user)) {
            Log::info('Could not log in user after password check');
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
