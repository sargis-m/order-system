<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Constants\OrderStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    use HasUserAuthorization;

    public static function configure(Table $table): Table
    {
        $instance = new static;
        $isAdmin = $instance->isAdmin();
        $isPartner = $instance->isPartner();
        $isCustomer = $instance->isCustomer();

        $columns = [
            TextColumn::make('id')
                ->label('ID')
                ->sortable(),            
        ];

        if (!$isCustomer) {
            $columns[] = TextColumn::make('customer.name')
            ->label('Customer')
            ->searchable()
            ->sortable();
        }

        if ($isAdmin) {
            $columns[] = TextColumn::make('partner.name')
                ->label('Partner')
                ->searchable()
                ->sortable()
                ->placeholder('N/A');
        }

        $columns[] = TextColumn::make('title')
            ->label('Title')
            ->limit(50)
            ->tooltip(fn ($record) => $record->title)
            ->sortable();

        $columns[] = TextColumn::make('status')
            ->badge()
            ->color(fn (string $state): string => match ($state) {
                OrderStatus::PENDING => 'warning',
                OrderStatus::ACCEPTED => 'success',
                OrderStatus::REJECTED => 'danger',
                default => 'gray',
            })
            ->sortable();

        $columns[] = TextColumn::make('total_amount')
            ->label('Total Amount')
            ->money('USD')
            ->sortable();        

        $columns[] = TextColumn::make('created_at')
            ->label('Created At')
            ->dateTime()
            ->sortable();

        $filters = [];

        if ($isAdmin) {
            $filters[] = SelectFilter::make('status')
                ->options(OrderStatus::all());
        }

        $actions = [];

        if ($isAdmin) {
            $actions[] = Action::make('accept')
                ->label('Accept')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->canBeAccepted())
                ->action(function ($record) {
                    try {
                        $record->accept();
                        Notification::make()
                            ->title('Order accepted successfully')
                            ->success()
                            ->send();
                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                });

            $actions[] = Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->canBeRejected())
                ->action(function ($record) {
                    try {
                        $record->reject();
                        Notification::make()
                            ->title('Order rejected')
                            ->warning()
                            ->send();
                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                });

            $actions[] = ViewAction::make()
                ->url(fn ($record) => OrderResource::getUrl('view', ['record' => $record]));
        } else {
            $actions[] = EditAction::make();
        }

        $toolbarActions = [];

        if (!$isAdmin) {
            $toolbarActions[] = BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]);
        }

        $tableConfig = $table
            ->columns($columns)
            ->filters($filters)
            ->recordActions($actions)
            ->toolbarActions($toolbarActions)
            ->defaultSort('created_at', 'desc');

        if ($isAdmin) {
            $tableConfig->recordUrl(fn ($record) => OrderResource::getUrl('view', ['record' => $record]));
        } else {
            $tableConfig->recordUrl(fn ($record) => OrderResource::getUrl('edit', ['record' => $record]));
        }

        return $tableConfig;
    }
}
