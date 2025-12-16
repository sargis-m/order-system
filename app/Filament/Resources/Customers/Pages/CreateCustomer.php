<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Constants\Role;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    use HasUserAuthorization;

    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->isPartner()) {
            $user = $this->getCurrentUser();
            if ($user) {
                $data['partner_id'] = $user->id;
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->assignRole(Role::CUSTOMER);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
