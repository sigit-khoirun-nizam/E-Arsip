<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $modelLabel = 'Kategori';
    protected static ?string $pluralModelLabel = 'Kategori';
    protected static ?string $navigationGroup = 'Manajemen Arsip';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Kategori')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(100),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('retention_years')
                    ->label('Masa Retensi (Tahun)')
                    ->numeric(),
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->label('Unit')
                    ->searchable()
                    ->preload()
                    ->default(fn() => auth()->user()->unit_id)
                    ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->required(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withCount([
                'archives as approaching_retention_count' => function (Builder $query) {
                    // MySQL specific raw query for calculating if the archive is approaching retention
                    // e.g. within 30 days of retention period.
                    $query->whereRaw('DATE_ADD(upload_date, INTERVAL categories.retention_years YEAR) <= ?', [now()->addDays(30)]);
                }
            ]);
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('categories.unit_id', auth()->user()->unit_id);
        }
        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('retention_years')
                    ->label('Masa Retensi (T)')
                    ->numeric()
                    ->sortable()
                    ->description(
                        fn(Category $record): ?string =>
                        $record->approaching_retention_count > 0
                        ? "{$record->approaching_retention_count} arsip hampir selesai!"
                        : null
                    )
                    ->color(
                        fn(Category $record): ?string =>
                        $record->approaching_retention_count > 0 ? 'danger' : null
                    )
                    ->icon(
                        fn(Category $record): ?string =>
                        $record->approaching_retention_count > 0 ? 'heroicon-o-exclamation-triangle' : null
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
