<?php

namespace App\Filament\Admin\Resources\ShopSettings;

use App\Filament\Admin\Resources\ShopSettings\Pages\CreateShopSetting;
use App\Filament\Admin\Resources\ShopSettings\Pages\EditShopSetting;
use App\Filament\Admin\Resources\ShopSettings\Pages\ListShopSettings;
use App\Filament\Admin\Resources\ShopSettings\Schemas\ShopSettingForm;
use App\Filament\Admin\Resources\ShopSettings\Tables\ShopSettingsTable;
use App\Models\ShopSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShopSettingResource extends Resource
{
    protected static ?string $model = ShopSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ShopSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopSettingsTable::configure($table);
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
            'index' => ListShopSettings::route('/'),
            'create' => CreateShopSetting::route('/create'),
            'edit' => EditShopSetting::route('/{record}/edit'),
        ];
    }
}
