<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Concerns\HasUserAuthorization;
use App\Models\Constants\OrderStatus;
use App\Models\Constants\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    use HasUserAuthorization;

    public static function configure(Schema $schema): Schema
    {
        $instance = new static;
        $user = $instance->getCurrentUser();
        $isAdmin = $instance->isAdmin();
        $isPartner = $instance->isPartner();

        $components = [];

        if ($isAdmin) {
            $components[] = Select::make('customer_id')
                ->label('Customer')
                ->relationship('customer', 'name')
                ->disabled()
                ->dehydrated();
        } elseif ($isPartner) {
            $components[] = Select::make('customer_id')
                ->label('Customer')
                ->relationship(
                    name: 'customer',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn ($query) => $user ? $query->where('partner_id', $user->id)->whereHas('roles', fn ($q) => $q->where('name', Role::CUSTOMER)) : $query
                )
                ->searchable()
                ->preload()
                ->required();
        }

        $components[] = Textarea::make('title')
            ->label('Title')
            ->rows(3)
            ->columnSpanFull()
            ->disabled($isAdmin)
            ->dehydrated();

        $components[] = TextInput::make('total_amount')
            ->label('Total Amount')
            ->numeric()
            ->prefix('$')
            ->default(0)
            ->minValue(0)
            ->maxValue(999999.99)
            ->step(0.01)
            ->required()
            ->disabled($isAdmin)
            ->dehydrated();

        if ($isAdmin) {
            $components[] = Select::make('status')
                ->label('Status')
                ->options(OrderStatus::all())
                ->disabled()
                ->dehydrated();
        }

        return $schema->components($components);
    }
}
