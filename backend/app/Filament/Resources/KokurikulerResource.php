<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KokurikulerResource\Pages;
use App\Filament\Resources\KokurikulerResource\RelationManagers;
use App\Models\Kokurikuler;
use App\Models\Semester;
use App\Models\Guru;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KokurikulerResource extends BaseResource
{
    protected static ?string $model = Kokurikuler::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationGroup(): ?string
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['kepala_sekolah', 'kepsek'])) {
            return 'Akademik Kepala Sekolah';
        }
        return $user->hasRole('ptk') ? 'Tugas Tambahan' : 'Kesiswaan';
    }

    public static function shouldRegisterNavigation(): bool
    {
         $user = auth()->user();
         if ($user->hasRole('super_admin')) {
             return false;
         }
         
         // Allow Wali Kelas to see this menu
         if ($user->hasRole('wali_kelas')) {
             return true;
         }
         
         if ($user->hasRole('ptk')) {
              return $user->guru && \App\Models\PembimbingKokurikuler::where('guru_id', $user->guru->id)->exists();
         }
         return parent::shouldRegisterNavigation();
    }

    protected static ?string $navigationLabel = 'Kokurikuler';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('ptk') && $user->guru) {
            $myIds = \App\Models\PembimbingKokurikuler::where('guru_id', $user->guru->id)->pluck('kokurikuler_id');
            $query->whereIn('id', $myIds);
        }

        return $query;
    }

    protected static ?string $modelLabel = 'Kokurikuler';

    protected static ?string $pluralModelLabel = 'Data Kokurikuler';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $canManageInfo = $user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepsek', 'tendik']);
        $canManageStatus = $user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepsek']);

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kokurikuler')
                    ->schema([
                        Forms\Components\Select::make('unit_id')
                            ->label('Unit')
                            ->options(Unit::pluck('nama', 'id'))
                            ->required()
                            ->searchable()
                            ->disabled(!$canManageInfo),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kokurikuler')
                            ->required()
                            ->maxLength(255)
                            ->disabled(!$canManageInfo),
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(65535)
                            ->disabled(!$canManageInfo),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->disabled(!$canManageStatus),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Daftar Pembimbing')
                    ->schema([
                        Forms\Components\Repeater::make('pembimbings')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('guru_id')
                                    ->label('Guru / Pembimbing')
                                    ->relationship('guru', 'nama_lengkap')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                        $set('nama_pembimbing', \App\Models\Guru::find($state)?->nama_lengkap)
                                    ),
                                Forms\Components\TextInput::make('nama_pembimbing')
                                    ->label('Nama Pembimbing')
                                    ->readOnly()
                                    ->required()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('no_telepon')
                                    ->tel()
                                    ->maxLength(255),
                            ])
                            ->itemLabel('Pembimbing')
                            ->addActionLabel('Tambah Pembimbing')
                            ->defaultItems(0)
                    ])
                    ->visible($canManageInfo),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nama')
                    ->label('Unit')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('pembimbings_count')
                    ->label('Pembimbing')
                    ->counts('pembimbings'),
                Tables\Columns\TextColumn::make('anggotas_count')
                    ->label('Peserta')
                    ->counts('anggotas'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->options(Unit::pluck('nama', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('print_rekap')
                    ->label('Print Rekap')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('print.absensi.kokurikuler', $record))
                    ->openUrlInNewTab(),
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
            RelationManagers\AnggotasRelationManager::class,
            RelationManagers\TopikKokurikulersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKokurikulers::route('/'),
            'create' => Pages\CreateKokurikuler::route('/create'),
            'edit' => Pages\EditKokurikuler::route('/{record}/edit'),
        ];
    }
}
