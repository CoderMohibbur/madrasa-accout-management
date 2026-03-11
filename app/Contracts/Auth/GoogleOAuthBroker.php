<?php

namespace App\Contracts\Auth;

use App\Data\Auth\GoogleOAuthUser;
use Illuminate\Http\RedirectResponse;

interface GoogleOAuthBroker
{
    public function redirect(): RedirectResponse;

    public function user(): GoogleOAuthUser;
}
