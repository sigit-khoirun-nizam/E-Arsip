<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchiveRackResource\Pages;
use App\Filament\Resources\ArchiveRackResource\RelationManagers;
use App\Models\ArchiveRack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArchiveRackResource extends Resource
{
    protected static ?string $model = ArchiveRack::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $modelLabel = 'Rak Arsip';
    protected static ?string $pluralModelLabel = 'Rak Arsip';
    protected static ?string $navigationGroup = 'Manajemen Arsip';
    protected static ?int $navigationSort = 4;

    // Hanya unit SDM & Umum (code 120) dan super_admin yang bisa melihat menu ini
    public static function isSdmOrAdmin(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) return true;
        return $user->unit && $user->unit->code === '120';
    }

    public static function canAccess(): bool { return static::isSdmOrAdmin(); }
    public static function canCreate(): bool { return static::isSdmOrAdmin(); }
    public static function canEdit($record): bool { return static::isSdmOrAdmin(); }
    public static function canDelete($record): bool { return static::isSdmOrAdmin(); }
    public static function canView($record): bool { return static::isSdmOrAdmin(); }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Rak')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('RAK-001'),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Rak')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('location')
                    ->label('Lokasi Fisik')
                    ->maxLength(150),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'full'   => 'Penuh',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\Select::make('managed_by_unit_id')
                    ->label('Dikelola Oleh Unit')
                    ->relationship('managedByUnit', 'name')
                    ->default(fn() => auth()->user()?->unit_id)
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Rak')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Rak')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('archive_boxes_count')
                    ->label('Jml Box')
                    ->counts('archiveBoxes')
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'full'   => 'Penuh',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\ArchiveBoxesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArchiveRacks::route('/'),
            'create' => Pages\CreateArchiveRack::route('/create'),
            'edit' => Pages\EditArchiveRack::route('/{record}/edit'),
        ];
    }
}
