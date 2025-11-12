<?php

namespace App\Filament\Admin\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id')
                    ->searchable(),
                TextColumn::make('carrier')
                    ->badge(),
                TextColumn::make('tracking_number')
                    ->searchable(),
                TextColumn::make('pickup_id')
                    ->searchable(),
                TextColumn::make('pickup_name')
                    ->searchable(),
                TextColumn::make('pickup_address')
                    ->searchable(),
                TextColumn::make('pickup_city')
                    ->searchable(),
                TextColumn::make('pickup_postal_code')
                    ->searchable(),
                TextColumn::make('pickup_country')
                    ->searchable(),
                TextColumn::make('label_pdf_url')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
