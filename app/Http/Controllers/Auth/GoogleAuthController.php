<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Auth\GoogleOAuthBroker;
use App\Http\Controllers\Controller;
use App\Services\Auth\GoogleSignInService;
use App\Services\MultiRole\MultiRoleContextResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class GoogleAuthController extends Controller
{
    private const FLOW_SESSION_KEY = 'auth.google.flow';

    public function redirect(Request $request, GoogleOAuthBroker $broker): RedirectResponse
    {
        $intent = in_array($request->query('intent'), ['public', 'donor', 'guardian'], true)
            ? (string) $request->query('intent')
            : 'public';

        $request->session()->put(self::FLOW_SESSION_KEY, [
            'mode' => 'signin',
            'intent' => $intent,
        ]);

        try {
            return $broker->redirect();
        } catch (Throwable $exception) {
            return redirect()->to(url()->previous() ?: route('login'))
                ->with('google_auth_warning', $exception->getMessage());
        }
    }

    public function link(Request $request, GoogleOAuthBroker $broker): RedirectResponse
    {
        $request->session()->put(self::FLOW_SESSION_KEY, [
            'mode' => 'link',
            'user_id' => $request->user()->getKey(),
        ]);

        try {
            return $broker->redirect();
        } catch (Throwable $exception) {
            return redirect()->route('profile.edit')
                ->with('google_link_warning', $exception->getMessage());
        }
    }

    public function callback(
        Request $request,
        GoogleOAuthBroker $broker,
        GoogleSignInService $googleSignInService,
        MultiRoleContextResolver $multiRoleContextResolver,
    ): RedirectResponse {
        $flow = $request->session()->pull(self::FLOW_SESSION_KEY, [
            'mode' => 'signin',
            'intent' => 'public',
        ]);

        try {
            $providerUser = $broker->user();
        } catch (Throwable $exception) {
            return $this->redirectForFailedCallback($flow, $exception->getMessage());
        }

        if (($flow['mode'] ?? 'signin') === 'link') {
            $user = $request->user();

            if (! $user || (int) ($flow['user_id'] ?? 0) !== $user->getKey()) {
                return redirect()->route('login')
                    ->with('google_auth_warning', 'The Google linking session expired. Sign in locally and start the link again from your profile.');
            }

            $outcome = $googleSignInService->linkAuthenticatedUser($user, $providerUser);

            return redirect()->route('profile.edit')
                ->with($outcome->failed() ? 'google_link_warning' : 'google_link_message', $outcome->message);
        }

        $outcome = $googleSignInService->signInWithGoogle(
            $providerUser,
            (string) ($flow['intent'] ?? 'public'),
        );

        if ($outcome->failed() || ! $outcome->shouldLogin || ! $outcome->user) {
            return redirect()->route('login')
                ->with('google_auth_warning', $outcome->message);
        }

        Auth::login($outcome->user);
        $request->session()->regenerate();

        return redirect()->intended(
            $this->defaultRedirectPath($outcome->user, $multiRoleContextResolver),
        )->with('google_auth_message', $outcome->message);
    }

    private function redirectForFailedCallback(array $flow, string $message): RedirectResponse
    {
        if (($flow['mode'] ?? 'signin') === 'link') {
            return redirect()->route('profile.edit')
                ->with('google_link_warning', $message);
        }

        return redirect()->route('login')
            ->with('google_auth_warning', $message);
    }

    private function defaultRedirectPath(
        \App\Models\User $user,
        MultiRoleContextResolver $multiRoleContextResolver,
    ): string {
        return route(
            $multiRoleContextResolver->defaultAuthRouteName($user),
            absolute: false,
        );
    }
}
