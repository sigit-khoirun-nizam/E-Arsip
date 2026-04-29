<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchiveResource\Pages;
use App\Filament\Resources\ArchiveResource\RelationManagers;
use App\Models\Archive;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArchiveResource extends Resource
{
    protected static ?string $model = Archive::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $modelLabel = 'Dokumen';
    protected static ?string $pluralModelLabel = 'Dokumen';
    protected static ?string $navigationGroup = 'Manajemen Arsip';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ordner_id')
                    ->label('Ordner')
                    ->options(function (\Filament\Forms\Get $get) {
                        return \App\Models\Ordner::with('category')
                            ->when($get('unit_id'), fn($query, $unitId) => $query->where('unit_id', $unitId))
                            ->when(!$get('unit_id') && !auth()->user()->hasRole('super_admin'), fn($query) => $query->where('unit_id', auth()->user()->unit_id))
                            ->get()
                            ->mapWithKeys(function ($ordner) {
                                $categoryName = $ordner->category ? $ordner->category->name : 'Tanpa Kategori';
                                return [$ordner->id => "{$ordner->code} - {$categoryName}"];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                        if (!$state) {
                            $set('category_id', null);
                            return;
                        }
                        $ordner = \App\Models\Ordner::with('category')->find($state);
                        if ($ordner && $ordner->category) {
                            $category = $ordner->category;
                            $set('category_id', $category->id);
                            $set('code', $ordner->code);
                        }
                    })
                    ->required(),
                Forms\Components\Hidden::make('category_id'),
                Forms\Components\Hidden::make('code'),
                
                Forms\Components\Section::make('Data Arsip (Banyak Entri Sekaligus)')
                    ->description('Masukkan judul, deskripsi, dan file untuk satu atau banyak arsip yang akan dimasukkan ke Ordner ini.')
                    ->visibleOn('create')
                    ->schema([
                        Forms\Components\Repeater::make('archive_entries')
                            ->label('Daftar Arsip')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('No Dokumen')
                                    ->required()
                                    ->maxLength(200),
                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('file_path')
                                    ->label('File')
                                    ->directory('arsip')
                                    ->maxSize(10240)
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png']),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->required(),
                    ]),
                
                Forms\Components\TextInput::make('title')
                    ->label('No Dokumen')
                    ->required()
                    ->maxLength(200)
                    ->visibleOn(['edit', 'view']),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull()
                    ->visibleOn(['edit', 'view']),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->directory('arsip')
                    ->maxSize(10240)
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'])
                    ->visibleOn(['edit', 'view']),

                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->label('Unit')
                    ->searchable()
                    ->preload()
                    ->default(fn() => auth()->user()->unit_id)
                    ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set) {
                        $set('ordner_id', null);
                        $set('category_id', null);
                        $set('code', null);
                        $set('pic_id', null);
                    })
                    ->dehydrated()
                    ->required(),

                Forms\Components\Hidden::make('uploaded_by')
                    ->default(fn() => auth()->id()),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Inaktif',
                        'permanent' => 'Permanen',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\Select::make('pic_id')
                    ->relationship(
                        'pic',
                        'name',
                        fn(Builder $query, \Filament\Forms\Get $get) =>
                        $get('unit_id')
                            ? $query->where('unit_id', $get('unit_id'))
                            : (auth()->user()->hasRole('super_admin') ? $query : $query->where('unit_id', auth()->user()->unit_id))
                    )
                    ->label('PIC/Penanggung Jawab')
                    ->searchable()
                    ->preload()
                    ->default(fn() => auth()->id())
                    ->formatStateUsing(function ($state) {
                        return $state ?? auth()->id();
                    })
                    ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->required(),
                Forms\Components\Toggle::make('is_confidential')
                    ->label('Arsip Rahasia')
                    ->required(),
                Forms\Components\DateTimePicker::make('upload_date')
                    ->label('Tanggal Upload')
                    ->default(now())
                    ->displayFormat('d/m/Y H:i:s')
                    ->timezone('Asia/Jakarta')
                    ->native(false)
                    ->required(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ordner.code')
                    ->label('Ordner')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('title')
                    ->label('No Dokumen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Diunggah Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'permanent' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Inaktif',
                        'permanent' => 'Permanen',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('pic.name')
                    ->label('PIC')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_confidential')
                    ->label('Rahasia')
                    ->boolean(),
                Tables\Columns\TextColumn::make('upload_date')
                    ->label('Tanggal Upload')
                    ->dateTime('d/m/Y H:i:s', 'Asia/Jakarta')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime('d/m/Y H:i:s', 'Asia/Jakarta')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui pada')
                    ->dateTime('d/m/Y H:i:s', 'Asia/Jakarta')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultGroup('ordner.code')
            ->groups([
                \Filament\Tables\Grouping\Group::make('ordner.code')
                    ->label('Ordner')
                    ->collapsible(),
                \Filament\Tables\Grouping\Group::make('category.name')
                    ->label('Kategori')
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ordner_id')
                    ->relationship('ordner', 'code')
                    ->label('Ordner')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_file')
                    ->label('Lihat File')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn(\App\Models\Archive $record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn(\App\Models\Archive $record) => $record->file_path !== null),
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
            'index' => Pages\ListArchives::route('/'),
            'create' => Pages\CreateArchive::route('/create'),
            'edit' => Pages\EditArchive::route('/{record}/edit'),
        ];
    }
}
