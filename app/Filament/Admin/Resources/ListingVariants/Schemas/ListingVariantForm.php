<?php

namespace App\Filament\Admin\Resources\ListingVariants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ListingVariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('listing_id')
                    ->relationship('listing', 'title')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('compare_at_price')
                    ->numeric(),
                TextInput::make('cost_price')
                    ->numeric(),
                TextInput::make('stock_qty')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_default')
                    ->required(),
                TextInput::make('weight_grams')
                    ->numeric(),
                TextInput::make('dimensions'),
                TextInput::make('barcode'),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
