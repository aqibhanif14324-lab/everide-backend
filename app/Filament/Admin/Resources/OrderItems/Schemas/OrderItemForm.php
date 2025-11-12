<?php

namespace App\Filament\Admin\Resources\OrderItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                Select::make('listing_id')
                    ->relationship('listing', 'title')
                    ->required(),
                Select::make('variant_id')
                    ->relationship('variant', 'id'),
                TextInput::make('title_snapshot')
                    ->required(),
                TextInput::make('sku_snapshot')
                    ->required(),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('line_total')
                    ->required()
                    ->numeric(),
                TextInput::make('selected_attributes'),
            ]);
    }
}
