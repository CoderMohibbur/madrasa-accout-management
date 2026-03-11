<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MultiRole\MultiRoleContextResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->to($this->verifiedRedirectPath(
                $user,
                app(MultiRoleContextResolver::class),
            ));
        }

        return view('auth.verify-email', [
            'user' => $user,
        ]);
    }

    private function verifiedRedirectPath(
        User $user,
        MultiRoleContextResolver $multiRoleContextResolver,
    ): string
    {
        return route(
            $multiRoleContextResolver->defaultVerifiedRouteName($user),
            absolute: false,
        );
    }
}
