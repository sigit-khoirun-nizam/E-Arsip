<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KorinResource\Pages;
use App\Filament\Resources\KorinResource\RelationManagers;
use App\Models\Korin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KorinResource extends Resource
{
    protected static ?string $model = Korin::class;

    protected static ?string $modelLabel = 'Korespondensi Internal';

    protected static ?string $pluralModelLabel = 'Korespondensi Internal';

    protected static ?string $navigationGroup = 'Korin';

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->required()
                    ->maxLength(100),
                Forms\Components\DatePicker::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->required(),
                Forms\Components\TextInput::make('perihal')
                    ->label('Perihal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('unit_pengirim_id')
                    ->label('Unit Pengirim')
                    ->relationship('unitPengirim', 'name')
                    ->default(fn() => auth()->user()?->unit_id)
                    ->disabled(fn() => auth()->user()?->unit_id !== null && !auth()->user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Baru' => 'Baru',
                        'Pending' => 'Pending',
                        'Selesai' => 'Selesai',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->default('Baru')
                    ->required(),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File Lampiran')
                    ->directory('korin')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('isi')
                    ->label('Isi Surat')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('dibuat_oleh')
                    ->default(fn() => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unitPengirim.name')
                    ->label('Unit Pengirim')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Dibuat Oleh')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Baru' => 'gray',
                        'Pending' => 'warning',
                        'Selesai' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn($state) => $state ? 'Download' : '-')
                    ->url(fn($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary'),
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
                Tables\Actions\Action::make('buat_disposisi')
                    ->label('Buat Disposisi')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->url(fn(\App\Models\Korin $record): string => \App\Filament\Resources\KorinDisposisiResource::getUrl('create', ['korin_id' => $record->id]))
                    ->visible(fn(\App\Models\Korin $record) => auth()->user()?->hasAnyRole(['super_admin', 'pengurus', 'pengurus ketua', 'pengurus bendahara', 'pengurus sekretaris']) && !$record->disposisis()->where('dari_user_id', auth()->id())->exists()),
                Tables\Actions\Action::make('revisi_disposisi')
                    ->label('Revisi Disposisi')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->url(fn(\App\Models\Korin $record): string => \App\Filament\Resources\KorinDisposisiResource::getUrl('edit', ['record' => $record->disposisis()->where('dari_user_id', auth()->id())->latest()->first()->id ?? $record->disposisis()->latest()->first()->id]))
                    ->visible(fn(\App\Models\Korin $record) => auth()->user()?->hasAnyRole(['super_admin', 'pengurus', 'pengurus ketua', 'pengurus bendahara', 'pengurus sekretaris']) && $record->status === 'Ditolak' && $record->disposisis()->where('dari_user_id', auth()->id())->exists()),
                Tables\Actions\Action::make('print')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(\App\Models\Korin $record): string => route('korins.print', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\DisposisisRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKorins::route('/'),
            'create' => Pages\CreateKorin::route('/create'),
            'view' => Pages\ViewKorin::route('/{record}'),
            'edit' => Pages\EditKorin::route('/{record}/edit'),
        ];
    }
}
