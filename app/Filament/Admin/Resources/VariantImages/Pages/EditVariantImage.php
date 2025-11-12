<?php

namespace App\Filament\Admin\Resources\VariantImages\Pages;

use App\Filament\Admin\Resources\VariantImages\VariantImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVariantImage extends EditRecord
{
    protected static string $resource = VariantImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
