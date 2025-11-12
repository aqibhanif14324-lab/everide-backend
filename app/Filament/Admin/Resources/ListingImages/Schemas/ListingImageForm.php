<?php

namespace App\Filament\Admin\Resources\ListingImages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ListingImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('listing_id')
                    ->relationship('listing', 'title')
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
