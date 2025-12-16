<?php

namespace App\Concerns;

use App\Models\Constants\Permission;
use App\Models\Constants\Role;
use App\Models\User;

trait HasUserAuthorization
{
    protected function getCurrentUser(): ?User
    {
        return auth()->user();
    }

    protected function isAdmin(): bool
    {
        return $this->getCurrentUser()?->hasRole(Role::ADMIN) ?? false;
    }

    protected function isPartner(): bool
    {
        return $this->getCurrentUser()?->hasRole(Role::PARTNER) ?? false;
    }

    protected function isCustomer(): bool
    {
        return $this->getCurrentUser()?->hasRole(Role::CUSTOMER) ?? false;
    }

    protected function hasPermission(string $permission): bool
    {
        return $this->getCurrentUser()?->hasPermissionTo($permission) ?? false;
    }

    protected function canViewAllOrders(): bool
    {
        return $this->hasPermission(Permission::VIEW_ALL_ORDERS);
    }

    protected function canViewOwnOrders(): bool
    {
        return $this->hasPermission(Permission::VIEW_OWN_ORDERS);
    }

    protected function canPlaceOrder(): bool
    {
        return $this->hasPermission(Permission::PLACE_ORDER);
    }

    protected function canAcceptOrders(): bool
    {
        return $this->hasPermission(Permission::ACCEPT_ORDERS);
    }

    protected function canRejectOrders(): bool
    {
        return $this->hasPermission(Permission::REJECT_ORDERS);
    }

    protected function canRegisterCustomer(): bool
    {
        return $this->hasPermission(Permission::REGISTER_CUSTOMER);
    }
}

