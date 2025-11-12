<?php

namespace App\Filament\Admin\Resources\Shipments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                Select::make('carrier')
                    ->options([
            'mondial_relay' => 'Mondial relay',
            'relais_colis' => 'Relais colis',
            'shop2shop' => 'Shop2shop',
            'colissimo' => 'Colissimo',
            'aggregator' => 'Aggregator',
        ])
                    ->required(),
                TextInput::make('tracking_number'),
                TextInput::make('pickup_id'),
                TextInput::make('pickup_name'),
                TextInput::make('pickup_address'),
                TextInput::make('pickup_city'),
                TextInput::make('pickup_postal_code'),
                TextInput::make('pickup_country'),
                TextInput::make('label_pdf_url')
                    ->url(),
                Select::make('status')
                    ->options([
            'created' => 'Created',
            'labeled' => 'Labeled',
            'in_transit' => 'In transit',
            'delivered' => 'Delivered',
            'exception' => 'Exception',
        ])
                    ->default('created')
                    ->required(),
            ]);
    }
}
