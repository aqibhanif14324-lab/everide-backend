<?php

namespace App\Filament\Admin\Resources\ShopSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ShopSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shop_id')
                    ->relationship('shop', 'name')
                    ->required(),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
                Textarea::make('shipping_policy')
                    ->columnSpanFull(),
                Textarea::make('return_policy')
                    ->columnSpanFull(),
                TextInput::make('logo_url')
                    ->url(),
            ]);
    }
}
