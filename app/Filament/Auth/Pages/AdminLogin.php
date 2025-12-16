<?php

namespace App\Filament\Auth\Pages;

use Filament\Auth\Pages\Login;

class AdminLogin extends Login
{
    public function getHeading(): string
    {
        return 'Sign in as Admin';
    }
}

