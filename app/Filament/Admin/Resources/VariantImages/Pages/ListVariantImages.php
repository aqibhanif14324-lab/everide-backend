<?php

namespace App\Filament\Admin\Resources\VariantImages\Pages;

use App\Filament\Admin\Resources\VariantImages\VariantImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVariantImages extends ListRecords
{
    protected static string $resource = VariantImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
