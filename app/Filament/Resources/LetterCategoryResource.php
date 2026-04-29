<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LetterCategoryResource\Pages;
use App\Filament\Resources\LetterCategoryResource\RelationManagers;
use App\Models\LetterCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LetterCategoryResource extends Resource
{
    protected static ?string $model = LetterCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'Kategori Surat';
    protected static ?string $pluralModelLabel = 'Kategori Surat';
    protected static ?string $navigationGroup = 'Surat menyurat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->label('Bagian/Unit')
                    ->searchable()
                    ->preload()
                    ->default(fn() => auth()->user()->unit_id)
                    ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->required(),
                Forms\Components\TextInput::make('kode_surat')
                    ->label('Kode Surat')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Kode Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kode_surat')
                    ->label('Kode Surat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->searchable(),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('unit_id', auth()->user()->unit_id);
        }
        return $query;
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
            'index' => Pages\ListLetterCategories::route('/'),
            'create' => Pages\CreateLetterCategory::route('/create'),
            'edit' => Pages\EditLetterCategory::route('/{record}/edit'),
        ];
    }
}
