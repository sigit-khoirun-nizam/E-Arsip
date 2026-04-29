<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengirimResource\Pages;
use App\Filament\Resources\PengirimResource\RelationManagers;
use App\Models\Pengirim;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengirimResource extends Resource
{
    protected static ?string $model = Pengirim::class;

    protected static ?string $modelLabel = 'Pengirim';

    protected static ?string $pluralModelLabel = 'Pengirim';

    protected static ?string $navigationGroup = 'Surat menyurat';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Pengirim')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('telepon')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(30),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\Textarea::make('alamat')
                    ->label('Alamat')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pengirim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telepon')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengirims::route('/'),
            'create' => Pages\CreatePengirim::route('/create'),
            'edit' => Pages\EditPengirim::route('/{record}/edit'),
        ];
    }
}
