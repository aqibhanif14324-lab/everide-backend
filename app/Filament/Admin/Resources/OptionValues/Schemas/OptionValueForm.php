<?php

namespace App\Filament\Admin\Resources\OptionValues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OptionValueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('option_id')
                    ->relationship('option', 'name')
                    ->required(),
                TextInput::make('value')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('meta'),
            ]);
    }
}
