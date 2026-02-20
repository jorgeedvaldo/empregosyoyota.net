<?php

namespace App\Filament\Resources\DraftResource\Pages;

use App\Filament\Resources\DraftResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDraft extends EditRecord
{
    protected static string $resource = DraftResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
