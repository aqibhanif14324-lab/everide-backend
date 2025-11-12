<?php

namespace App\Filament\Admin\Resources\Options;

use App\Filament\Admin\Resources\Options\Pages\CreateOption;
use App\Filament\Admin\Resources\Options\Pages\EditOption;
use App\Filament\Admin\Resources\Options\Pages\ListOptions;
use App\Filament\Admin\Resources\Options\Schemas\OptionForm;
use App\Filament\Admin\Resources\Options\Tables\OptionsTable;
use App\Models\Option;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OptionResource extends Resource
{
    protected static ?string $model = Option::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return OptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OptionsTable::configure($table);
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
            'index' => ListOptions::route('/'),
            'create' => CreateOption::route('/create'),
            'edit' => EditOption::route('/{record}/edit'),
        ];
    }
}
