<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdnerResource\Pages;
use App\Models\Ordner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdnerResource extends Resource
{
    protected static ?string $model = Ordner::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $modelLabel = 'Ordner';
    protected static ?string $pluralModelLabel = 'Ordner';
    protected static ?string $navigationGroup = 'Manajemen Arsip';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->label('Kode Unit')
                    ->searchable()
                    ->preload()
                    ->default(fn() => auth()->user()->unit_id)
                    ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name', fn(Builder $query) => auth()->user()->hasRole('super_admin') ? $query : $query->where('unit_id', auth()->user()->unit_id))
                    ->label('Jenis Ordner')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state, \Filament\Forms\Get $get) {
                        if (!$state) return;
                        $category = \App\Models\Category::find($state);
                        if ($category) {
                            $periodStr = $get('period_start') ?: date('Y-m');
                            $year = substr($periodStr, 0, 4) ?: date('Y');

                            $latestCode = \App\Models\Ordner::where('category_id', $category->id)
                                ->where('period', 'like', "{$year}-%")
                                ->orderBy('id', 'desc')
                                ->value('code');

                            $nextNumber = 1;
                            if ($latestCode && preg_match('/\/(\d{3,})\//', $latestCode, $matches)) {
                                $nextNumber = intval($matches[1]) + 1;
                            } else {
                                $nextNumber = \App\Models\Ordner::where('category_id', $category->id)->where('period', 'like', "{$year}-%")->count() + 1;
                            }
                            $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                            $newCode = "{$category->code}/{$formattedNumber}/{$year}";
                            
                            $set('code', $newCode);
                        }
                    })
                    ->required(),
                Forms\Components\Select::make('letter_type_id')
                    ->relationship(
                        'letterType',
                        'name',
                        fn(Builder $query, \Filament\Forms\Get $get) =>
                        $get('unit_id')
                            ? $query->where('unit_id', $get('unit_id'))
                            : (auth()->user()->hasRole('super_admin') ? $query : $query->where('unit_id', auth()->user()->unit_id))
                    )
                    ->allowHtml()
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\LetterType $record) => 
                        "<span class='inline-block px-2 py-0.5 rounded-full text-white text-xs font-bold' style='background-color: " . ($record->unit?->color ?? '#6b7280') . "'>" . $record->name . "</span>"
                    )
                    ->label('Jenis Surat')
                    ->searchable()
                    ->preload()
                    ->default(fn() => \App\Models\LetterType::where('unit_id', auth()->user()->unit_id)->first()?->id)
                    ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ->dehydrated()
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->label('No')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('period_start')
                        ->label('Dari Bulan')
                        ->type('month')
                        ->required()
                        ->formatStateUsing(function (?\App\Models\Ordner $record) {
                            if (!$record || !$record->period) return date('Y-m');
                            $parts = explode(' - ', $record->period);
                            return $parts[0] ?? date('Y-m');
                        })
                        ->default(date('Y-m'))
                        ->dehydrated(false)
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (\Filament\Forms\Set $set, $state, \Filament\Forms\Get $get) {
                            $start = $state;
                            $end = $get('period_end');
                            if ($start && $end) {
                                $set('period', "$start - $end");
                            }
                            $categoryId = $get('category_id');
                            if (!$categoryId || !$state) return;
                            $category = \App\Models\Category::find($categoryId);
                            if ($category) {
                                $year = substr($state, 0, 4) ?: date('Y');

                                $latestCode = \App\Models\Ordner::where('category_id', $category->id)
                                    ->where('period', 'like', "{$year}-%")
                                    ->orderBy('id', 'desc')
                                    ->value('code');

                                $nextNumber = 1;
                                if ($latestCode && preg_match('/\/(\d{3,})\//', $latestCode, $matches)) {
                                    $nextNumber = intval($matches[1]) + 1;
                                } else {
                                    $nextNumber = \App\Models\Ordner::where('category_id', $category->id)->where('period', 'like', "{$year}-%")->count() + 1;
                                }
                                $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                                $newCode = "{$category->code}/{$formattedNumber}/{$year}";
                                
                                $set('code', $newCode);
                            }
                        }),
                    Forms\Components\TextInput::make('period_end')
                        ->label('Sampai Bulan')
                        ->type('month')
                        ->required()
                        ->formatStateUsing(function (?\App\Models\Ordner $record) {
                            if (!$record || !$record->period) return date('Y-m');
                            $parts = explode(' - ', $record->period);
                            return $parts[1] ?? date('Y-m');
                        })
                        ->default(date('Y-m'))
                        ->dehydrated(false)
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (\Filament\Forms\Set $set, $state, \Filament\Forms\Get $get) {
                            $start = $get('period_start');
                            $end = $state;
                            if ($start && $end) {
                                $set('period', "$start - $end");
                            }
                        }),
                ]),
                Forms\Components\Hidden::make('period')
                    ->default(date('Y-m') . ' - ' . date('Y-m'))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\DatePicker::make('retention_expires_at')
                    ->label('Tanggal Kadaluarsa Retensi')
                    ->helperText('Kosongkan jika mengikuti otomatis dari kategori')
                    ->nullable()
                    ->displayFormat('d/m/Y')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        $userUnit = $user->unit;

        // super_admin dan unit SDM & Umum (code 120) bisa melihat semua ordner
        $isSdm = $userUnit && $userUnit->code === '120';
        if (!$user->hasRole('super_admin') && !$isSdm) {
            $query->where('unit_id', $user->unit_id);
        }

        // Sembunyikan ordner yang sudah kadaluarsa retensi DAN sudah dipindah ke rak arsip
        $query->where(function ($q) {
            $q->whereNull('retention_expires_at')
              ->orWhere('retention_expires_at', '>', now())
              ->orWhereNull('archive_box_id'); // Jika expired tapi belum masuk rak, masih muncul
        });

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Kode Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Jenis Ordner')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('letterType.name')
                    ->label('Jenis Surat')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        return '<div style="width: 1rem; height: 1rem; border-radius: 9999px; background-color: ' . ($record->unit?->color ?? '#6b7280') . ';" title="' . $state . '"></div>';
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $parts = explode(' - ', $state);
                        if (count($parts) == 2) {
                            $start = \Carbon\Carbon::parse($parts[0].'-01')->translatedFormat('F Y');
                            $end = \Carbon\Carbon::parse($parts[1].'-01')->translatedFormat('F Y');
                            return "$start - $end";
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('status')
                     ->badge()
                     ->label('Status')
                     ->color(fn(string $state): string => match ($state) {
                         'active' => 'success',
                         'inactive' => 'danger',
                         default => 'gray',
                     })
                     ->formatStateUsing(fn(string $state): string => match ($state) {
                         'active' => 'Aktif',
                         'inactive' => 'Tidak Aktif',
                         default => $state,
                     }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->label('Export')
                    ->exports([
                        \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Excel')
                            ->withFilename('Data_Ordner_'.date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->askForFilename(),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('pindah_ke_box')
                    ->label('Pindah ke Box Arsip')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('warning')
                    ->visible(fn (\App\Models\Ordner $record) =>
                        $record->isRetentionExpired() && is_null($record->archive_box_id)
                    )
                    ->form([
                        Forms\Components\Select::make('archive_box_id')
                            ->label('Pilih Box Arsip')
                            ->options(\App\Models\ArchiveBox::where('status', 'active')->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (\App\Models\Ordner $record, array $data) {
                        $record->update([
                            'archive_box_id' => $data['archive_box_id'],
                            'status' => 'inactive',
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Ordner berhasil dipindahkan ke Box Arsip')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('download_pdf')
                    ->label('Label PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(fn (\App\Models\Ordner $record) => route('ordners.print', $record->id))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                        ->label('Export Selected')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Excel')
                                ->withFilename('Data_Ordner_Terpilih_'.date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                                ->askForFilename(),
                        ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdnerResource\RelationManagers\ArchivesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdners::route('/'),
            'create' => Pages\CreateOrdner::route('/create'),
            'view' => Pages\ViewOrdner::route('/{record}'),
            'edit' => Pages\EditOrdner::route('/{record}/edit'),
        ];
    }
}
