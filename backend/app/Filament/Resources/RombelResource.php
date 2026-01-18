<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RombelResource\Pages;
use App\Filament\Resources\RombelResource\RelationManagers;
use App\Models\Rombel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasUnitFiltering;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RombelResource extends BaseResource
{
    use HasUnitFiltering;

    protected static ?string $model = Rombel::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Master Data Sekolah';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Rombel';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'nama')
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('ruang_kelas_id')
                    ->relationship('ruangKelas', 'nama', function (Builder $query, Forms\Get $get) {
                        if ($unitId = $get('unit_id')) {
                            $query->where('unit_id', $unitId);
                        }
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $ruangKelas = \App\Models\RuangKelas::find($state);
                            if ($ruangKelas) {
                                $set('nama', $ruangKelas->nama);
                                // Extract tingkat from name (e.g. "Kelas 1B" -> 1)
                                if (preg_match('/(\d+)/', $ruangKelas->nama, $matches)) {
                                    $set('tingkat', (int)$matches[1]);
                                } else {
                                    $set('tingkat', 0); // Default for TK etc
                                }
                            }
                        }
                    }),
                Forms\Components\Select::make('tahun_ajaran_id')
                    ->relationship('tahunAjaran', 'nama')
                    ->required(),
                Forms\Components\Select::make('wali_kelas_id')
                    ->label('Wali Kelas')
                    ->options(function (Forms\Get $get) {
                        $unitId = $get('unit_id');
                        if (!$unitId) return [];
                        return \App\Models\Guru::where('unit_id', $unitId)
                            ->pluck('nama_lengkap', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->afterStateHydrated(function (Forms\Components\Select $component, $record) {
                        if (!$record) return;
                        $semester = \App\Models\Semester::getActive(); 
                        $wali = null;
                        
                        if ($semester) {
                            $wali = \App\Models\WaliKelas::where('rombel_id', $record->id)
                                ->where('semester_id', $semester->id)
                                ->first();
                        }
                        
                        if (!$wali) {
                             $wali = \App\Models\WaliKelas::where('rombel_id', $record->id)->latest()->first();
                        }

                        if ($wali) {
                            $component->state($wali->guru_id);
                        }
                    })
                    ->dehydrated(false),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->hidden()
                    ->dehydrated(),
                Forms\Components\TextInput::make('tingkat')
                    ->required()
                    ->numeric()
                    ->hidden()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nama')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangKelas.nama')
                    ->label('Ruang Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Rombel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tingkat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('siswas_count')
                    ->counts('siswas')
                    ->label('Jml Siswa'),
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
            RelationManagers\SiswasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRombels::route('/'),
            'create' => Pages\CreateRombel::route('/create'),
            'edit' => Pages\EditRombel::route('/{record}/edit'),
        ];
    }
}
