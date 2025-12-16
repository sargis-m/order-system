<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Customers\CustomerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    use HasUserAuthorization;

    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public static function canAccess(array $parameters = []): bool
    {
        $instance = new static;
        $user = $instance->getCurrentUser();
        $record = $parameters['record'] ?? null;

        if (!$user || !$record) {
            return false;
        }

        if ($instance->isPartner()) {
            return $record->partner_id === $user->id;
        }

        return false;
    }
}
