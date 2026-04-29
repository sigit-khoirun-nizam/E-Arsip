<?php

namespace App\Filament\Resources\ArchiveResource\Pages;

use App\Filament\Resources\ArchiveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArchive extends CreateRecord
{
    protected static string $resource = ArchiveResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $entries = $data['archive_entries'] ?? [];
        unset($data['archive_entries']);

        $firstModel = null;
        
        if (empty($entries)) {
            // fallback behavior if repeater is skipped
            return parent::handleRecordCreation($data);
        }

        foreach ($entries as $entry) {
            $recordData = $data;
            $recordData['title'] = $entry['title'] ?? '-';
            $recordData['description'] = $entry['description'] ?? null;
            $recordData['file_path'] = $entry['file_path'] ?? null;
            
            $model = parent::handleRecordCreation($recordData);
            if (! $firstModel) {
                $firstModel = $model;
            }
        }

        return $firstModel;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

