<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    use HasUserAuthorization;

    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->isAdmin()) {
            return $this->record->getAttributes();
        }

        if (isset($data['status'])) {
            $data['status'] = $this->record->status;
        }

        return $data;
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        if ($this->isAdmin()) {
            Notification::make()
                ->title('Orders cannot be modified')
                ->body('You can only accept or reject orders, not modify them.')
                ->warning()
                ->send();
            return;
        }

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    protected function getFormActions(): array
    {
        if ($this->isAdmin()) {
            return [];
        }

        return parent::getFormActions();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
        $user = $instance->getCurrentUser();

        if (!$user) {
            return false;
        }

        $record = $parameters['record'] ?? null;

        if (!$record) {
            return false;
        }

        if ($instance->isAdmin()) {
            return false;
        }

        if ($instance->isPartner()) {
            return $record->partner_id === $user->id;
        }

        if ($instance->isCustomer()) {
            return $record->customer_id === $user->id;
        }

        return false;
    }
}
