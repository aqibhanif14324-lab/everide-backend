<?php

namespace App\Filament\Admin\Resources\Options\Pages;

use App\Filament\Admin\Resources\Options\OptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOption extends EditRecord
{
    protected static string $resource = OptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
