<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutgoingLetterResource\Pages;
use App\Filament\Resources\OutgoingLetterResource\RelationManagers;
use App\Models\OutgoingLetter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OutgoingLetterResource extends Resource
{
    protected static ?string $model = OutgoingLetter::class;

    protected static ?string $modelLabel = 'Surat Keluar';

    protected static ?string $pluralModelLabel = 'Surat Keluar';

    protected static ?string $navigationGroup = 'Surat menyurat';

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Surat')
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                        self::updateNomorSurat($set, $get);
                    }),
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name')
                    ->required()
                    ->default(fn() => auth()->user()->unit_id)
                    ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                        self::updateNomorSurat($set, $get);
                    }),
                Forms\Components\Select::make('letter_category_id')
                    ->label('Kategori Surat')
                    ->relationship(
                        'letterCategory', 
                        'kode_surat',
                        fn(Builder $query, \Filament\Forms\Get $get) =>
                        $get('unit_id')
                            ? $query->where('unit_id', $get('unit_id'))
                            : (auth()->user()->hasRole('super_admin') ? $query : $query->where('unit_id', auth()->user()->unit_id))
                    )
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\LetterCategory $record) => "{$record->kode_surat} - {$record->deskripsi}")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                        self::updateNomorSurat($set, $get);
                    }),
                Forms\Components\TextInput::make('nomor_surat')
                    ->label('Nomor Surat (Automatis - Terkini)')
                    ->required()
                    ->readOnly()
                    ->maxLength(100)
                    ->extraAttributes([
                        'wire:poll.3s' => 'refreshNomorSurat',
                    ])
                    ->suffixAction(
                        \Filament\Forms\Components\Actions\Action::make('refresh')
                            ->icon('heroicon-m-arrow-path')
                            ->tooltip('Perbarui Antrean')
                            ->action(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                self::updateNomorSurat($set, $get);
                            })
                    ),
                Forms\Components\TextInput::make('kepada')
                    ->label('Kepada')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('perihal')
                    ->label('Perihal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('referensi')
                    ->label('Referensi')
                    ->maxLength(100),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File Dokumen')
                    ->directory('surat_keluar')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Surat')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('letterCategory.kode_surat')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kepada')
                    ->label('Kepada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('referensi')
                    ->label('Referensi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('Dokumen')
                    ->formatStateUsing(fn($state) => $state ? 'Download' : '-')
                    ->url(fn($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->searchable(false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutgoingLetters::route('/'),
            'create' => Pages\CreateOutgoingLetter::route('/create'),
            'edit' => Pages\EditOutgoingLetter::route('/{record}/edit'),
        ];
    }

    public static function updateNomorSurat(\Filament\Forms\Set $set, \Filament\Forms\Get $get)
    {
        $tanggal = $get('tanggal');
        $unitId = $get('unit_id');
        $letterCategoryId = $get('letter_category_id');

        if (!$tanggal || !$unitId || !$letterCategoryId) {
            $set('nomor_surat', null);
            return;
        }

        $unit = \App\Models\Unit::find($unitId);
        $category = \App\Models\LetterCategory::find($letterCategoryId);

        if (!$unit || !$category) {
            $set('nomor_surat', null);
            return;
        }

        $datePart = \Carbon\Carbon::parse($tanggal)->format('m.Y');
        $month = \Carbon\Carbon::parse($tanggal)->month;
        $year = \Carbon\Carbon::parse($tanggal)->year;

        // Logic for Antrian (sequence count)
        $count = \App\Models\OutgoingLetter::where('unit_id', $unitId)
            ->where('letter_category_id', $letterCategoryId)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->count() + 1; // Basic count mechanism

        // Attempt to find latest number to ensure sequence
        $latestLetter = \App\Models\OutgoingLetter::where('unit_id', $unitId)
            ->where('letter_category_id', $letterCategoryId)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestLetter && preg_match('/^(\d{3})\./', $latestLetter->nomor_surat, $matches)) {
            $count = intval($matches[1]) + 1;
        }

        $antrian = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        // 001.140/PDG.02/04.2026
        $nomorSurat = "{$antrian}.{$unit->code}/{$category->kode_surat}/{$datePart}";
        $set('nomor_surat', $nomorSurat);
    }
}
