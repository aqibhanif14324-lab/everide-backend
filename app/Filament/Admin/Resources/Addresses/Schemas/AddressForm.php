<?php

namespace App\Filament\Admin\Resources\Addresses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('line1')
                    ->required(),
                TextInput::make('line2'),
                TextInput::make('city')
                    ->required(),
                TextInput::make('postal_code')
                    ->required(),
                TextInput::make('country')
                    ->required(),
                Toggle::make('is_default')
                    ->required(),
            ]);
    }
}
