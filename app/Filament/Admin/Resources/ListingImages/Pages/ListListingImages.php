<?php

namespace App\Filament\Admin\Resources\ListingImages\Pages;

use App\Filament\Admin\Resources\ListingImages\ListingImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListListingImages extends ListRecords
{
    protected static string $resource = ListingImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
