<?php

namespace App\Filament\Resources\ArchiveShelfResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrdnersRelationManager extends RelationManager
{
    protected static string $relationship = 'ordners';
    protected static ?string $title = 'Ordner dalam Box Ini';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')->label('No Ordner')->disabled(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('No Ordner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $parts = explode(' - ', $state);
                        if (count($parts) == 2) {
                            $start = \Carbon\Carbon::parse($parts[0] . '-01')->translatedFormat('F Y');
                            $end = \Carbon\Carbon::parse($parts[1] . '-01')->translatedFormat('F Y');
                            return "$start - $end";
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('retention_expires_at')
                    ->label('Expired Retensi')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('archives_count')
                    ->label('Jml Dokumen')
                    ->counts('archives')
                    ->sortable(),
            ])
            ->headerActions([
                // Ordner masuk ke rak melalui aksi di OrdnerResource
            ])
            ->actions([
                Tables\Actions\Action::make('remove_from_shelf')
                    ->label('Keluarkan dari Box')
                    ->icon('heroicon-o-arrow-left-start-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['archive_box_id' => null]);
                    }),
            ]);
    }
}
