<?php

namespace App\Filament\Resources\OutgoingLetterResource\Pages;

use App\Filament\Resources\OutgoingLetterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOutgoingLetter extends CreateRecord
{
    protected static string $resource = OutgoingLetterResource::class;

    // This method is called automatically every 3 seconds via Livewire polling (setup in the form placeholder)
    public function refreshNomorSurat()
    {
        $tanggal = $this->data['tanggal'] ?? null;
        $unitId = $this->data['unit_id'] ?? null;
        $letterCategoryId = $this->data['letter_category_id'] ?? null;

        if (!$tanggal || !$unitId || !$letterCategoryId) {
            return;
        }

        $unit = \App\Models\Unit::find($unitId);
        $category = \App\Models\LetterCategory::find($letterCategoryId);

        if (!$unit || !$category) {
            return;
        }

        $parsedDate = \Carbon\Carbon::parse($tanggal);
        $datePart = $parsedDate->format('m.Y');
        $month = $parsedDate->month;
        $year = $parsedDate->year;

        $latestLetter = \App\Models\OutgoingLetter::where('unit_id', $unit->id)
            ->where('letter_category_id', $category->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('id', 'desc')
            ->first();

        $count = \App\Models\OutgoingLetter::where('unit_id', $unit->id)
            ->where('letter_category_id', $category->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->count() + 1;

        if ($latestLetter && preg_match('/^(\d{3})\./', $latestLetter->nomor_surat, $matches)) {
            $count = intval($matches[1]) + 1;
        }

        $antrian = str_pad($count, 3, '0', STR_PAD_LEFT);
        $newNomorSurat = "{$antrian}.{$unit->code}/{$category->kode_surat}/{$datePart}";
        
        $currentSurat = $this->data['nomor_surat'] ?? null;
        if ($currentSurat !== $newNomorSurat) {
            $this->data['nomor_surat'] = $newNomorSurat;
            // Force fill the form component to ensure it updates visually
            $this->form->fill($this->data);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate the actual nomor_surat exactly before insert
        $tanggal = \Carbon\Carbon::parse($data['tanggal']);
        $datePart = $tanggal->format('m.Y');
        $month = $tanggal->month;
        $year = $tanggal->year;

        $unit = \App\Models\Unit::find($data['unit_id']);
        $category = \App\Models\LetterCategory::find($data['letter_category_id']);

        if ($unit && $category) {
            $latestLetter = \App\Models\OutgoingLetter::where('unit_id', $unit->id)
                ->where('letter_category_id', $category->id)
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->lockForUpdate() // Avoid concurrent insert race condition if using transactions
                ->orderBy('id', 'desc')
                ->first();

            $count = \App\Models\OutgoingLetter::where('unit_id', $unit->id)
                ->where('letter_category_id', $category->id)
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->count() + 1;

            if ($latestLetter && preg_match('/^(\d{3})\./', $latestLetter->nomor_surat, $matches)) {
                $count = intval($matches[1]) + 1;
            }

            $antrian = str_pad($count, 3, '0', STR_PAD_LEFT);
            $newNomorSurat = "{$antrian}.{$unit->code}/{$category->kode_surat}/{$datePart}";
            
            // Check if it's relying on the UI preview or an explicitly forced number
            if ($data['nomor_surat'] !== $newNomorSurat) {
                $this->data['nomor_surat'] = $newNomorSurat;
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'nomor_surat' => 'Antrean telah berubah. Silakan tekan Simpan kembali jika setuju menjadi: ' . $newNomorSurat,
                ]);
            }

            $data['nomor_surat'] = $newNomorSurat;
        }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

