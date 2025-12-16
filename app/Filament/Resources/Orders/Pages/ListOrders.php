<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    use HasUserAuthorization;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        if ($this->canPlaceOrder()) {
            return [
                CreateAction::make(),
            ];
        }

        return [];
    }
}
