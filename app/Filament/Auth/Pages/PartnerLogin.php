<?php

namespace App\Filament\Auth\Pages;

use Filament\Auth\Pages\Login;

class PartnerLogin extends Login
{
    public function getHeading(): string
    {
        return 'Sign in as Partner';
    }
}
