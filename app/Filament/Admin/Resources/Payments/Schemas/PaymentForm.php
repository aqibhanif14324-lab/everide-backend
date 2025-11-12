<?php

namespace App\Filament\Admin\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                Select::make('provider')
                    ->options(['stripe' => 'Stripe', 'mangopay' => 'Mangopay', 'other' => 'Other'])
                    ->required(),
                TextInput::make('payment_intent_id'),
                TextInput::make('transaction_id'),
                Select::make('status')
                    ->options([
            'created' => 'Created',
            'authorized' => 'Authorized',
            'captured' => 'Captured',
            'refunded' => 'Refunded',
            'failed' => 'Failed',
        ])
                    ->default('created')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
                TextInput::make('raw_payload'),
            ]);
    }
}
