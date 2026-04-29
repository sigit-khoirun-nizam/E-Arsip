<?php

namespace App\Filament\Resources\KorinResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DisposisisRelationManager extends RelationManager
{
    protected static string $relationship = 'disposisis';

    protected static ?string $title = 'Disposisi Korin';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('dari_user_id')
                    ->default(fn() => auth()->id())
                    ->required(),
                Forms\Components\Select::make('ke_user_id')
                    ->label('Penerima Disposisi')
                    ->relationship('penerima', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(fn(\Filament\Resources\RelationManagers\RelationManager $livewire) => $livewire->getOwnerRecord()->dibuat_oleh)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\RichEditor::make('catatan')
                    ->label('Catatan')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Setuju' => 'Setuju',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->default('Pending')
                    ->required(),
                Forms\Components\DateTimePicker::make('tanggal_disposisi')
                    ->label('Tanggal Disposisi')
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                Tables\Columns\TextColumn::make('pengirim.name')
                    ->label('Pengirim')
                    ->sortable(),
                Tables\Columns\TextColumn::make('penerima.name')
                    ->label('Penerima Disposisi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Setuju' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tanggal_disposisi')
                    ->label('Tgl Disposisi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn($livewire) => auth()->user()?->hasAnyRole(['super_admin', 'pengurus', 'pengurus ketua', 'pengurus bendahara', 'pengurus sekretaris']) && !$livewire->getOwnerRecord()->disposisis()->where('dari_user_id', auth()->id())->exists()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'pengurus', 'pengurus ketua', 'pengurus bendahara', 'pengurus sekretaris'])),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'pengurus', 'pengurus ketua', 'pengurus bendahara', 'pengurus sekretaris'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
