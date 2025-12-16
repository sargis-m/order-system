<?php

namespace App\Filament\Resources\Customers;

use App\Concerns\HasUserAuthorization;
use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Filament\Resources\Customers\Tables\CustomersTable;
use App\Models\Constants\Role;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    use HasUserAuthorization;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Customers';

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Customers';

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $resource = new static;
        return $resource->canRegisterCustomer();
    }

    public static function canCreate(): bool
    {
        $resource = new static;
        return $resource->canRegisterCustomer();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $resource = new static;
        $user = $resource->getCurrentUser();

        if (!$user) {
            return $query->whereNull('id');
        }

        if ($resource->isPartner()) {
            return $query->where('partner_id', $user->id)
                ->whereHas('roles', function ($q) {
                    $q->where('name', Role::CUSTOMER);
                })
                ->withCount('orders');
        }

        return $query->whereNull('id');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $resource = new static;
        return $resource->canRegisterCustomer();
    }
}
