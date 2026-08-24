<?php

namespace App\Http\Controllers\National\Eregistry;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirect to Keycloak login page.
     */
    // public function redirect(): RedirectResponse
    // {
    //     return Socialite::driver('keycloak')
    //         ->setScopes(['openid', 'profile', 'email'])
    //         ->redirect();
    // }

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('keycloak')
            ->redirect();
    }

    /**
     * Handle the callback from Keycloak.
     */
    public function callback(): RedirectResponse
    {
        // dd(request()->all());
        try {
            $keycloakUser = Socialite::driver('keycloak')->stateless()->user();
        } catch (Exception $e) {
            return redirect('/login')
                ->with('error', 'Authentication failed: ' . $e->getMessage());
        }

        $keycloakUser = Socialite::driver('keycloak')->stateless()->user();

        $user = User::where('keycloak_id', $keycloakUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $keycloakUser->getEmail())->first();
        }

        if ($user) {
            $user->update([
                'keycloak_id' => $keycloakUser->getId(),
                'first_name'  => $user->first_name ?: ($keycloakUser->user['given_name'] ?? ''),
                'last_name'   => $user->last_name ?: ($keycloakUser->user['family_name'] ?? ''),
            ]);
        } else {
            $user = User::create([
                'keycloak_id' => $keycloakUser->getId(),
                'first_name'  => $keycloakUser->user['given_name'] ?? '',
                'last_name'   => $keycloakUser->user['family_name'] ?? '',
                'email'       => $keycloakUser->getEmail(),
                'password'    => bcrypt(Str::random(32)),
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Log the user out and end the Keycloak session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $baseUrl = config('services.keycloak.base_url');
        $realm = config('services.keycloak.realms');
        $clientId = config('services.keycloak.client_id');
        $redirectUri = urlencode(config('app.url'));

        $logoutUrl = "{$baseUrl}/realms/{$realm}/protocol/openid-connect/logout"
            . "?client_id={$clientId}"
            . "&post_logout_redirect_uri={$redirectUri}";

        return redirect($logoutUrl);
    }

    /**
     * Sync Keycloak roles to the local user.
     */
    // private function syncRoles(User $user, string $accessToken): void
    // {
    //     try {
    //         $tokenParts = explode('.', $accessToken);
    //         $payload = json_decode(
    //             base64_decode(strtr($tokenParts[1], '-_', '+/')),
    //             true
    //         );

    //         $roles = $payload['realm_access']['roles'] ?? [];

    //         // Filter to application-relevant roles
    //         $appRoles = array_intersect(
    //             $roles,
    //             ['user', 'editor', 'admin']
    //         );

    //         $user->syncKeycloakRoles($appRoles);
    //     } catch (Exception $e) {
    //         logger()->warning(
    //             'Failed to sync Keycloak roles',
    //             ['error' => $e->getMessage()]
    //         );
    //     }
    // }
}