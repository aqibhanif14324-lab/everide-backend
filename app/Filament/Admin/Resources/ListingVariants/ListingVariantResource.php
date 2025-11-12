<?php

namespace App\Filament\Admin\Resources\ListingVariants;

use App\Filament\Admin\Resources\ListingVariants\Pages\CreateListingVariant;
use App\Filament\Admin\Resources\ListingVariants\Pages\EditListingVariant;
use App\Filament\Admin\Resources\ListingVariants\Pages\ListListingVariants;
use App\Filament\Admin\Resources\ListingVariants\Schemas\ListingVariantForm;
use App\Filament\Admin\Resources\ListingVariants\Tables\ListingVariantsTable;
use App\Models\ListingVariant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ListingVariantResource extends Resource
{
    protected static ?string $model = ListingVariant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ListingVariantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ListingVariantsTable::configure($table);
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
            'index' => ListListingVariants::route('/'),
            'create' => CreateListingVariant::route('/create'),
            'edit' => EditListingVariant::route('/{record}/edit'),
        ];
    }
}
