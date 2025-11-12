<?php

namespace App\Filament\Admin\Resources\Listings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shop_id')
                    ->relationship('shop', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->relationship('category', 'name'),
                TextInput::make('brand'),
                TextInput::make('model'),
                TextInput::make('year')
                    ->numeric(),
                Select::make('condition')
                    ->options(['new' => 'New', 'very_good' => 'Very good', 'good' => 'Good', 'for_parts' => 'For parts'])
                    ->default('good')
                    ->required(),
                Select::make('status')
                    ->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'reserved' => 'Reserved',
            'sold' => 'Sold',
            'archived' => 'Archived',
        ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at'),
                TextInput::make('default_price')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
                TextInput::make('location_city'),
                TextInput::make('location_country'),
            ]);
    }
}
