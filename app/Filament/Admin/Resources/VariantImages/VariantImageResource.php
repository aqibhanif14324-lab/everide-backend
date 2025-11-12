<?php

namespace App\Filament\Admin\Resources\VariantImages;

use App\Filament\Admin\Resources\VariantImages\Pages\CreateVariantImage;
use App\Filament\Admin\Resources\VariantImages\Pages\EditVariantImage;
use App\Filament\Admin\Resources\VariantImages\Pages\ListVariantImages;
use App\Filament\Admin\Resources\VariantImages\Schemas\VariantImageForm;
use App\Filament\Admin\Resources\VariantImages\Tables\VariantImagesTable;
use App\Models\VariantImage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VariantImageResource extends Resource
{
    protected static ?string $model = VariantImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'no';

    public static function form(Schema $schema): Schema
    {
        return VariantImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VariantImagesTable::configure($table);
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
            'index' => ListVariantImages::route('/'),
            'create' => CreateVariantImage::route('/create'),
            'edit' => EditVariantImage::route('/{record}/edit'),
        ];
    }
}
