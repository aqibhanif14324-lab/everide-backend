<?php

namespace App\Filament\Admin\Resources\Listings;

use App\Filament\Admin\Resources\Listings\Pages\CreateListing;
use App\Filament\Admin\Resources\Listings\Pages\EditListing;
use App\Filament\Admin\Resources\Listings\Pages\ListListings;
use App\Filament\Admin\Resources\Listings\Pages\ViewListing;
use App\Filament\Admin\Resources\Listings\Schemas\ListingForm;
use App\Filament\Admin\Resources\Listings\Schemas\ListingInfolist;
use App\Filament\Admin\Resources\Listings\Tables\ListingsTable;
use App\Models\Listing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'shop settings';

    public static function form(Schema $schema): Schema
    {
        return ListingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ListingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ListingsTable::configure($table);
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
            'index' => ListListings::route('/'),
            'create' => CreateListing::route('/create'),
            'view' => ViewListing::route('/{record}'),
            'edit' => EditListing::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
