<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Response;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    $sheet->setCellValue('A1', 'code');
                    $sheet->setCellValue('B1', 'name');
                    $sheet->setCellValue('C1', 'description');
                    $sheet->setCellValue('D1', 'retention_years');
                    
                    $sheet->setCellValue('A2', 'KAT-001');
                    $sheet->setCellValue('B2', 'Kategori 1');
                    $sheet->setCellValue('C2', 'Deskripsi Kategori 1');
                    $sheet->setCellValue('D2', '5');
                    
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    
                    return response()->streamDownload(function () use ($writer) {
                        $writer->save('php://output');
                    }, 'template_import_kategori.xlsx', [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]);
                }),
            Actions\Action::make('import_excel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('attachment')
                        ->label('Upload File Excel')
                        ->directory('imports')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filePath = Storage::disk('public')->path($data['attachment']);
                    
                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('Gagal import. File tidak ditemukan.')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                        $sheet = $spreadsheet->getActiveSheet();
                        $rows = $sheet->toArray();
                        
                        $count = 0;
                        foreach ($rows as $index => $row) {
                            if ($index === 0) continue; // skip header
                            
                            if (count($row) >= 2 && !empty($row[0])) { 
                                Category::create([
                                    'code' => $row[0] ?? null,
                                    'name' => $row[1] ?? null,
                                    'description' => $row[2] ?? null,
                                    'retention_years' => isset($row[3]) && is_numeric($row[3]) ? $row[3] : 0,
                                    'unit_id' => auth()->user()->unit_id,
                                ]);
                                $count++;
                            }
                        }

                        Notification::make()
                            ->title("Berhasil mengimport {$count} kategori.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                         Notification::make()
                            ->title('Gagal membaca file Excel.')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
