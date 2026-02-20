<?php

namespace App\Filament\Resources\DraftResource\Pages;

use App\Filament\Resources\DraftResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrafts extends ListRecords
{
    protected static string $resource = DraftResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
