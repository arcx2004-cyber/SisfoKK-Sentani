<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EkstrakurikulerResource\Pages;
use App\Filament\Resources\EkstrakurikulerResource\RelationManagers;
use App\Models\Ekstrakurikuler;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EkstrakurikulerResource extends BaseResource
{
    protected static ?string $model = Ekstrakurikuler::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    
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
            // Only show if assigned as Pelatih
            return $user->guru && \App\Models\PelatihEkskul::where('guru_id', $user->guru->id)->exists();
        }
        // Admin/Tendik logic (Kesiswaan group allowed in BaseResource)
        return parent::shouldRegisterNavigation();
    }
    
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Ekstrakurikuler';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('ptk') && $user->guru) {
            $myEkskulIds = \App\Models\PelatihEkskul::where('guru_id', $user->guru->id)->pluck('ekstrakurikuler_id');
            $query->whereIn('id', $myEkskulIds);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $canManageInfo = $user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepsek', 'tendik']);
        $canManageStatus = $user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepsek']);

        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'nama')
                    ->required()
                    ->disabled(!$canManageInfo),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->disabled(!$canManageInfo),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull()
                    ->disabled(!$canManageInfo),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->disabled(!$canManageStatus),
                
                Forms\Components\Section::make('Daftar Pelatih / Pembina')
                    ->schema([
                        Forms\Components\Repeater::make('pelatihEkskuls')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('guru_id')
                                    ->label('Guru / Pembina')
                                    ->relationship('guru', 'nama_lengkap')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                        $set('nama_pelatih', \App\Models\Guru::find($state)?->nama_lengkap)
                                    ),
                                Forms\Components\TextInput::make('nama_pelatih')
                                    ->label('Nama Pelatih')
                                    ->readOnly()
                                    ->required()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('no_telepon')
                                    ->tel()
                                    ->maxLength(255),
                            ])
                            ->itemLabel('Pelatih')
                            ->addActionLabel('Tambah Pelatih')
                            ->defaultItems(0) // Start empty if new
                    ])
                    ->visible($canManageInfo)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nama')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                Tables\Actions\Action::make('print_rekap')
                    ->label('Print Rekap')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('print.absensi.ekskul', $record))
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
            RelationManagers\RombelEkskulsRelationManager::class,
            RelationManagers\KegiatanEkskulsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEkstrakurikulers::route('/'),
            'create' => Pages\CreateEkstrakurikuler::route('/create'),
            'edit' => Pages\EditEkstrakurikuler::route('/{record}/edit'),
        ];
    }
}
