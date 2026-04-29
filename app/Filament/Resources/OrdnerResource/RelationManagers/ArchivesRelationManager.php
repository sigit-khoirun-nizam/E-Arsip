<?php

namespace App\Filament\Resources\OrdnerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArchivesRelationManager extends RelationManager
{
    protected static string $relationship = 'archives';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('No Dokumen')
                    ->required()
                    ->maxLength(200),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->directory('arsip')
                    ->maxSize(10240)
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png']),
                Forms\Components\Hidden::make('uploaded_by')
                    ->default(fn() => auth()->id()),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Inaktif',
                        'permanent' => 'Permanen',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\Select::make('pic_id')
                    ->relationship(
                        'pic',
                        'name',
                        fn(Builder $query) =>
                        auth()->user()->hasRole('super_admin') ? $query : $query->where('unit_id', auth()->user()->unit_id)
                    )
                    ->label('PIC/Penanggung Jawab')
                    ->searchable()
                    ->preload()
                    ->default(fn() => auth()->id())
                    ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->required(),
                Forms\Components\Toggle::make('is_confidential')
                    ->label('Arsip Rahasia')
                    ->required(),
                Forms\Components\DateTimePicker::make('upload_date')
                    ->label('Tanggal Upload')
                    ->default(now())
                    ->displayFormat('d/m/Y H:i:s')
                    ->timezone('Asia/Jakarta')
                    ->native(false)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('No Dokumen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'permanent' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Inaktif',
                        'permanent' => 'Permanen',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Diunggah Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_confidential')
                    ->label('Rahasia')
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Excel')
                            ->withFilename('Data_Arsip_Ordner_'.date('Y-m-d'))
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('code')->heading('Kode Arsip'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('title')->heading('No Dokumen'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('status')->heading('Status'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('uploader.name')->heading('Diunggah Oleh'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('upload_date')->heading('Tanggal Upload'),
                            ])
                            ->modifyQueryUsing(fn (Builder $query) => $query->where('ordner_id', $this->getOwnerRecord()->id))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->askForFilename(),
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make('PDF')
                            ->withFilename('Data_Arsip_Ordner_'.date('Y-m-d'))
                            ->withColumns([
                                \pxlrbt\FilamentExcel\Columns\Column::make('code')->heading('Kode Arsip'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('title')->heading('No Dokumen'),
                                \pxlrbt\FilamentExcel\Columns\Column::make('status')->heading('Status'),
                            ])
                            ->modifyQueryUsing(fn (Builder $query) => $query->where('ordner_id', $this->getOwnerRecord()->id))
                            ->withWriterType(\Maatwebsite\Excel\Excel::DOMPDF)
                            ->askForFilename(),
                    ]),
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['code'] = $this->getOwnerRecord()->code;
                        $data['category_id'] = $this->getOwnerRecord()->category_id;
                        $data['unit_id'] = $this->getOwnerRecord()->unit_id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_file')
                    ->label('Lihat File')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn(\App\Models\Archive $record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn(\App\Models\Archive $record) => $record->file_path !== null),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->label('Export Selected')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Excel')
                                ->withFilename('Data_Arsip_Ordner_Terpilih_'.date('Y-m-d'))
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('code')->heading('Kode Arsip'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('title')->heading('No Dokumen'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('description')->heading('Desk'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('status')->heading('Status'),
                                ])
                                ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                                ->askForFilename(),
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('PDF')
                                ->withFilename('Data_Arsip_Ordner_Terpilih_'.date('Y-m-d'))
                                ->withColumns([
                                    \pxlrbt\FilamentExcel\Columns\Column::make('code')->heading('Kode Arsip'),
                                    \pxlrbt\FilamentExcel\Columns\Column::make('title')->heading('No Dokumen'),
                                ])
                                ->withWriterType(\Maatwebsite\Excel\Excel::DOMPDF)
                                ->askForFilename(),
                        ]),
                ]),
            ]);
    }
}
