<?php

namespace App\Filament\Resources\Orders;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Constants\OrderStatus;
use App\Models\Order;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    use HasUserAuthorization;

    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Order ID'),
                        TextEntry::make('customer.name')
                            ->label('Customer'),
                        TextEntry::make('customer.email')
                            ->label('Customer Email'),
                        TextEntry::make('partner.name')
                            ->label('Partner')
                            ->placeholder('N/A'),
                        TextEntry::make('title')
                            ->label('Title')
                            ->columnSpanFull(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                OrderStatus::PENDING => 'warning',
                                OrderStatus::ACCEPTED => 'success',
                                OrderStatus::REJECTED => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('USD'),                        
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $resource = new static;
        
        if ($resource->canViewAllOrders()) {
            return true;
        }

        return $resource->canViewOwnOrders();
    }

    public static function canCreate(): bool
    {
        $resource = new static;
        return $resource->canPlaceOrder();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['customer', 'partner']);
        
        $resource = new static;
        $user = $resource->getCurrentUser();

        if (!$user) {
            return $query->whereNull('id');
        }

        if ($resource->canViewAllOrders()) {
            return $query;
        }

        if ($resource->isPartner()) {
            return $query->where('partner_id', $user->id);
        }

        if ($resource->isCustomer()) {
            return $query->where('customer_id', $user->id);
        }

        return $query->whereNull('id');
    }

    public static function canEdit($record): bool
    {
        $resource = new static;
        $user = $resource->getCurrentUser();

        if (!$user) {
            return false;
        }

        if ($resource->isAdmin()) {
            return false;
        }

        if ($resource->isPartner()) {
            return $record->partner_id === $user->id;
        }

        if ($resource->isCustomer()) {
            return $record->customer_id === $user->id;
        }

        return false;
    }
}
