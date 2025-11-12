<?php

namespace App\Filament\Admin\Resources\ListingVariants\Pages;

use App\Filament\Admin\Resources\ListingVariants\ListingVariantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListListingVariants extends ListRecords
{
    protected static string $resource = ListingVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
