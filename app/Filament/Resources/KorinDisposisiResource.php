<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KorinDisposisiResource\Pages;
use App\Filament\Resources\KorinDisposisiResource\RelationManagers;
use App\Models\KorinDisposisi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KorinDisposisiResource extends Resource
{
    protected static ?string $model = KorinDisposisi::class;

    protected static ?string $modelLabel = 'Disposisi Korin';

    protected static ?string $pluralModelLabel = 'Disposisi Korin';

    protected static ?string $navigationGroup = 'Korin';

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'pengurus', 'pengurus ketua', 'pengurus bendahara', 'pengurus sekretaris']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('korin_id')
                    ->label('ID Korin')
                    ->relationship(
                        name: 'korin',
                        titleAttribute: 'nomor_surat',
                        modifyQueryUsing: fn(Builder $query, string $operation) => $operation === 'create' ? $query->whereDoesntHave('disposisis', fn($q) => $q->where('dari_user_id', auth()->id())) : $query
                    )
                    ->default(request()->query('korin_id'))
                    ->disabled(fn() => request()->has('korin_id'))
                    ->dehydrated()
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $korin = \App\Models\Korin::find($state);
                            if ($korin) {
                                $set('ke_user_id', $korin->dibuat_oleh);
                            }
                        }
                    }),
                Forms\Components\Hidden::make('dari_user_id')
                    ->default(fn() => auth()->id())
                    ->required(),
                Forms\Components\Select::make('ke_user_id')
                    ->label('Penerima Disposisi')
                    ->relationship('penerima', 'name')
                    ->default(function () {
                        if (request()->has('korin_id')) {
                            $korin = \App\Models\Korin::find(request()->query('korin_id'));
                            return $korin ? $korin->dibuat_oleh : null;
                        }
                        return null;
                    })
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->searchable()
                    ->preload(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('korin.nomor_surat')
                    ->label('ID Korin')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListKorinDisposisis::route('/'),
            'create' => Pages\CreateKorinDisposisi::route('/create'),
            'edit' => Pages\EditKorinDisposisi::route('/{record}/edit'),
        ];
    }
}