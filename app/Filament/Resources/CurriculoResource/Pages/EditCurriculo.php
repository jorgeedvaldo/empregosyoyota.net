<?php

namespace App\Filament\Resources\CurriculoResource\Pages;

use App\Filament\Resources\CurriculoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurriculo extends EditRecord
{
    protected static string $resource = CurriculoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
