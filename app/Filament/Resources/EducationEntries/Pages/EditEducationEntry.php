<?php

namespace App\Filament\Resources\EducationEntries\Pages;

use App\Filament\Resources\EducationEntries\EducationEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEducationEntry extends EditRecord
{
    protected static string $resource = EducationEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
