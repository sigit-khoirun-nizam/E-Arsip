<?php

namespace App\Filament\Resources\ArchiveRackResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArchiveBoxesRelationManager extends RelationManager
{
    protected static string $relationship = 'archiveBoxes';
    protected static ?string $title = 'Box Arsip di Rak Ini';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Box')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Box')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('location')
                    ->label('Lokasi Spesifik')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Box')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Box')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordners_count')
                    ->label('Jml Ordner')
                    ->counts('ordners')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger'  => 'full',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'active' => 'Aktif',
                        'full' => 'Penuh',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Box Baru')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['status'] = 'active';
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('remove_from_rack')
                    ->label('Keluarkan dari Rak')
                    ->icon('heroicon-o-arrow-left-start-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['archive_rack_id' => null]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
