<?php

namespace App\Filament\Admin\Resources\VariantImages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VariantImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('variant_id')
                    ->relationship('variant', 'id')
                    ->required(),
                TextInput::make('url')
                    ->url()
                    ->required(),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
