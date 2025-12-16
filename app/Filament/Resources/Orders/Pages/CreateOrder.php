<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Constants\OrderStatus;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    use HasUserAuthorization;

    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return $data;
        }

        if ($this->isCustomer()) {
            $data['customer_id'] = $user->id;
            $data['partner_id'] = null;
        }

        if ($this->isPartner()) {
            $data['partner_id'] = $user->id;
        }

        $data['status'] = OrderStatus::PENDING;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
