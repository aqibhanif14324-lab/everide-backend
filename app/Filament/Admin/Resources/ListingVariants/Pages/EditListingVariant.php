<?php

namespace App\Filament\Admin\Resources\ListingVariants\Pages;

use App\Filament\Admin\Resources\ListingVariants\ListingVariantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditListingVariant extends EditRecord
{
    protected static string $resource = ListingVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
