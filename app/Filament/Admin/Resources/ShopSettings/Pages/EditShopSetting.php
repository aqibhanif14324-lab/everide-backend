<?php

namespace App\Filament\Admin\Resources\ShopSettings\Pages;

use App\Filament\Admin\Resources\ShopSettings\ShopSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShopSetting extends EditRecord
{
    protected static string $resource = ShopSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
