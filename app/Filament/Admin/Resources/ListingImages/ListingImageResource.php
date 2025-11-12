<?php

namespace App\Filament\Admin\Resources\ListingImages;

use App\Filament\Admin\Resources\ListingImages\Pages\CreateListingImage;
use App\Filament\Admin\Resources\ListingImages\Pages\EditListingImage;
use App\Filament\Admin\Resources\ListingImages\Pages\ListListingImages;
use App\Filament\Admin\Resources\ListingImages\Schemas\ListingImageForm;
use App\Filament\Admin\Resources\ListingImages\Tables\ListingImagesTable;
use App\Models\ListingImage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ListingImageResource extends Resource
{
    protected static ?string $model = ListingImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'no';

    public static function form(Schema $schema): Schema
    {
        return ListingImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ListingImagesTable::configure($table);
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
            'index' => ListListingImages::route('/'),
            'create' => CreateListingImage::route('/create'),
            'edit' => EditListingImage::route('/{record}/edit'),
        ];
    }
}
