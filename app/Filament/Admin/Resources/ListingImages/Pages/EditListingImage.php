<?php

namespace App\Filament\Admin\Resources\ListingImages\Pages;

use App\Filament\Admin\Resources\ListingImages\ListingImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditListingImage extends EditRecord
{
    protected static string $resource = ListingImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
