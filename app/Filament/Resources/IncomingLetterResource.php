<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomingLetterResource\Pages;
use App\Filament\Resources\IncomingLetterResource\RelationManagers;
use App\Models\IncomingLetter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncomingLetterResource extends Resource
{
    protected static ?string $model = IncomingLetter::class;

    protected static ?string $modelLabel = 'Surat Masuk';

    protected static ?string $pluralModelLabel = 'Surat Masuk';

    protected static ?string $navigationGroup = 'Surat menyurat';

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_surat')
                    ->label('Nomor Surat')
                    ->required()
                    ->maxLength(100),
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Surat')
                    ->required(),
                Forms\Components\Select::make('pengirim_id')
                    ->label('Pengirim')
                    ->relationship('pengirim', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('referensi')
                    ->label('Referensi')
                    ->maxLength(100),
                Forms\Components\TextInput::make('tentang')
                    ->label('Tentang')
                    ->maxLength(255),
                Forms\Components\Hidden::make('status')
                    ->default('baru'),
                Forms\Components\FileUpload::make('dokumen')
                    ->label('File Dokumen')
                    ->directory('surat_masuk')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('disposisi')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_surat')
                    ->label('Nomor Surat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Surat')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengirim.nama')
                    ->label('Pengirim')
                    ->sortable(),
                Tables\Columns\TextColumn::make('referensi')
                    ->label('Referensi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tentang')
                    ->label('Tentang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dokumen')
                    ->label('Dokumen')
                    ->formatStateUsing(fn($state) => $state ? 'Download' : '-')
                    ->url(fn($record) => $record->dokumen ? asset('storage/' . $record->dokumen) : null)
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
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListIncomingLetters::route('/'),
            'create' => Pages\CreateIncomingLetter::route('/create'),
            'edit' => Pages\EditIncomingLetter::route('/{record}/edit'),
        ];
    }
}
