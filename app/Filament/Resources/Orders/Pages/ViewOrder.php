<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    use HasUserAuthorization;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];
        $record = $this->getRecord();

        if ($this->canAcceptOrders() && $record->canBeAccepted()) {
            $actions[] = Action::make('accept')
                ->label('Accept Order')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        $this->record->accept();
                        Notification::make()
                            ->title('Order accepted successfully')
                            ->success()
                            ->send();
                        $this->redirect($this->getResource()::getUrl('index'));
                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                });
        }

        if ($this->canRejectOrders() && $record->canBeRejected()) {
            $actions[] = Action::make('reject')
                ->label('Reject Order')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        $this->record->reject();
                        Notification::make()
                            ->title('Order rejected')
                            ->warning()
                            ->send();
                        $this->redirect($this->getResource()::getUrl('index'));
                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                });
        }

        return $actions;
    }

    public static function canAccess(array $parameters = []): bool
    {
        $instance = new static;
        return $instance->isAdmin();
    }
}
